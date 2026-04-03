<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenWork;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\OpenWork\Account;
use EasyWeChat\OpenWork\Application;
use EasyWeChat\OpenWork\AuthorizerAccessToken;
use EasyWeChat\OpenWork\Contracts\Account as AccountInterface;
use EasyWeChat\OpenWork\Contracts\Application as ApplicationInterface;
use EasyWeChat\OpenWork\Contracts\SuiteTicket as SuiteTicketInterface;
use EasyWeChat\OpenWork\Encryptor;
use EasyWeChat\OpenWork\JsApiTicket;
use EasyWeChat\OpenWork\ProviderAccessToken;
use EasyWeChat\OpenWork\Server;
use EasyWeChat\OpenWork\SuiteAccessToken;
use EasyWeChat\OpenWork\SuiteEncryptor;
use EasyWeChat\OpenWork\SuiteTicket;
use EasyWeChat\Tests\TestCase;
use Overtrue\Socialite\Providers\OpenWeWork;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ApplicationTest extends TestCase
{
    public function test_get_and_set_account()
    {
        $app = new Application($this->createAppConfig());

        $this->assertInstanceOf(ApplicationInterface::class, $app);
        $this->assertInstanceOf(AccountInterface::class, $app->getAccount());
        $this->assertSame($app->getAccount(), $app->getAccount());

        $account = new Account(
            corpId: 'wx3cf0f39249000060',
            providerSecret: 'mock-provider-secret',
            suiteId: 'suite-id',
            suiteSecret: 'mock-suite-secret',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        );

        $app->setAccount($account);

        $this->assertSame($account, $app->getAccount());
    }

    public function test_set_account_refreshes_default_dependencies()
    {
        $app = new Application($this->createAppConfig());

        $app->setCache(new Psr16Cache(new ArrayAdapter));

        $clientResponse = new MockResponse('{}');
        $app->setHttpClient(new MockHttpClient([
            new MockResponse(\json_encode([
                'provider_access_token' => 'first-provider-token',
                'expires_in' => 7200,
            ])),
            new MockResponse(\json_encode([
                'suite_access_token' => 'first-suite-token',
                'expires_in' => 7200,
            ])),
            new MockResponse(\json_encode([
                'provider_access_token' => 'second-provider-token',
                'expires_in' => 7200,
            ])),
            new MockResponse(\json_encode([
                'suite_access_token' => 'second-suite-token',
                'expires_in' => 7200,
            ])),
            $clientResponse,
        ], 'https://qyapi.weixin.qq.com/'));

        $firstProviderAccessToken = $app->getProviderAccessToken();
        $this->assertSame('first-provider-token', $firstProviderAccessToken->getToken());

        $firstSuiteTicket = $app->getSuiteTicket();
        $firstSuiteTicket->setTicket('first-suite-ticket');

        $firstSuiteAccessToken = $app->getSuiteAccessToken();
        $this->assertSame('first-suite-token', $firstSuiteAccessToken->getToken());

        $firstClient = $app->getClient();
        $firstServer = $app->getServer();

        $nextAccount = new Account(
            corpId: 'wx9876543210987654',
            providerSecret: 'mock-provider-secret-2',
            suiteId: 'suite-id-2',
            suiteSecret: 'mock-suite-secret-2',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        );
        $nextSuiteEncryptor = new SuiteEncryptor(
            suiteId: $nextAccount->getSuiteId(),
            token: $nextAccount->getToken(),
            aesKey: $nextAccount->getAesKey(),
        );

        $app->setAccount($nextAccount);

        $secondSuiteTicket = $app->getSuiteTicket();
        $secondSuiteTicket->setTicket('second-suite-ticket');

        $secondProviderAccessToken = $app->getProviderAccessToken();
        $this->assertSame('second-provider-token', $secondProviderAccessToken->getToken());

        $secondSuiteAccessToken = $app->getSuiteAccessToken();
        $this->assertSame('second-suite-token', $secondSuiteAccessToken->getToken());

        $secondClient = $app->getClient();
        $secondClient->request('GET', 'cgi-bin/service/get_provider_token');

        $app->setRequest($this->createEncryptedXmlMessageRequest('<xml>
            <SuiteId><![CDATA[suite-id-2]]></SuiteId>
            <InfoType><![CDATA[suite_ticket]]></InfoType>
            <TimeStamp>1403610513</TimeStamp>
            <SuiteTicket><![CDATA[persisted-suite-ticket]]></SuiteTicket>
        </xml>', $nextSuiteEncryptor));

        $secondServer = $app->getServer();
        $response = $secondServer->serve();

        $this->assertNotSame($firstProviderAccessToken, $secondProviderAccessToken);
        $this->assertNotSame($firstSuiteTicket, $secondSuiteTicket);
        $this->assertNotSame($firstSuiteAccessToken, $secondSuiteAccessToken);
        $this->assertNotSame($firstClient, $secondClient);
        $this->assertNotSame($firstServer, $secondServer);
        $this->assertSame(
            'https://qyapi.weixin.qq.com/cgi-bin/service/get_provider_token?provider_access_token=second-provider-token',
            $clientResponse->getRequestUrl()
        );
        $this->assertSame('success', (string) $response->getBody());
        $this->assertSame('persisted-suite-ticket', $secondSuiteTicket->getTicket());
    }

    public function test_get_and_set_encryptors()
    {
        $app = new Application($this->createAppConfig());

        $this->assertInstanceOf(Encryptor::class, $app->getEncryptor());
        $this->assertInstanceOf(SuiteEncryptor::class, $app->getSuiteEncryptor());

        $providerEncryptor = new Encryptor(
            corpId: 'wx3cf0f39249000060',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        );
        $suiteEncryptor = new SuiteEncryptor(
            suiteId: 'suite-id',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        );

        $app->setEncryptor($providerEncryptor);
        $app->setSuiteEncryptor($suiteEncryptor);

        $this->assertSame($providerEncryptor, $app->getEncryptor());
        $this->assertSame($suiteEncryptor, $app->getSuiteEncryptor());
    }

    public function test_get_and_set_request_and_server()
    {
        $app = new Application($this->createAppConfig());

        $this->assertInstanceOf(ServerRequestInterface::class, $app->getRequest());
        $this->assertInstanceOf(ServerInterface::class, $app->getServer());

        $request = \Mockery::mock(ServerRequestInterface::class);
        $server = new Server(
            encryptor: $app->getSuiteEncryptor(),
            providerEncryptor: $app->getEncryptor(),
            request: $request,
        );

        $app->setRequest($request);
        $app->setServer($server);

        $this->assertSame($request, $app->getRequest());
        $this->assertSame($server, $app->getServer());
    }

    public function test_get_and_set_client()
    {
        $app = new Application($this->createAppConfig());

        $this->assertInstanceOf(AccessTokenAwareClient::class, $app->getClient());
        $this->assertSame($app->getClient(), $app->getClient());

        $client = new AccessTokenAwareClient;
        $app->setClient($client);

        $this->assertSame($client, $app->getClient());
    }

    public function test_get_and_set_suite_ticket_and_access_tokens()
    {
        $app = new Application($this->createAppConfig());

        $this->assertInstanceOf(SuiteTicketInterface::class, $app->getSuiteTicket());
        $this->assertInstanceOf(AccessTokenInterface::class, $app->getProviderAccessToken());
        $this->assertInstanceOf(AccessTokenInterface::class, $app->getSuiteAccessToken());

        $suiteTicket = new SuiteTicket('suite-id', $app->getCache());
        $providerAccessToken = new ProviderAccessToken(
            corpId: 'wx3cf0f39249000060',
            providerSecret: 'mock-provider-secret',
        );
        $suiteAccessToken = new SuiteAccessToken(
            suiteId: 'suite-id',
            suiteSecret: 'mock-suite-secret',
            suiteTicket: $suiteTicket,
        );

        $this->assertSame($suiteTicket, $app->setSuiteTicket($suiteTicket));
        $app->setProviderAccessToken($providerAccessToken);
        $app->setSuiteAccessToken($suiteAccessToken);

        $this->assertSame($suiteTicket, $app->getSuiteTicket());
        $this->assertSame($providerAccessToken, $app->getProviderAccessToken());
        $this->assertSame($suiteAccessToken, $app->getSuiteAccessToken());
    }

    public function test_set_account_preserves_custom_dependencies()
    {
        $app = new Application($this->createAppConfig());

        $providerEncryptor = new Encryptor(
            corpId: 'wx3cf0f39249000060',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        );
        $suiteEncryptor = new SuiteEncryptor(
            suiteId: 'suite-id',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        );
        $server = \Mockery::mock(ServerInterface::class);
        $providerAccessToken = \Mockery::mock(AccessTokenInterface::class);
        $suiteAccessToken = \Mockery::mock(AccessTokenInterface::class);
        $suiteTicket = new SuiteTicket('suite-id', $app->getCache());
        $client = new AccessTokenAwareClient;

        $app->setEncryptor($providerEncryptor);
        $app->setSuiteEncryptor($suiteEncryptor);
        $app->setServer($server);
        $app->setProviderAccessToken($providerAccessToken);
        $app->setSuiteAccessToken($suiteAccessToken);
        $app->setSuiteTicket($suiteTicket);
        $app->setClient($client);

        $app->setAccount(new Account(
            corpId: 'wx9876543210987654',
            providerSecret: 'mock-provider-secret-2',
            suiteId: 'suite-id-2',
            suiteSecret: 'mock-suite-secret-2',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        ));

        $this->assertSame($providerEncryptor, $app->getEncryptor());
        $this->assertSame($suiteEncryptor, $app->getSuiteEncryptor());
        $this->assertSame($server, $app->getServer());
        $this->assertSame($providerAccessToken, $app->getProviderAccessToken());
        $this->assertSame($suiteAccessToken, $app->getSuiteAccessToken());
        $this->assertSame($suiteTicket, $app->getSuiteTicket());
        $this->assertSame($client, $app->getClient());
    }

    public function test_set_provider_access_token_refreshes_resolved_client()
    {
        $app = new Application($this->createAppConfig());

        $firstAccessToken = \Mockery::mock(AccessTokenInterface::class);
        $firstAccessToken->shouldReceive('toQuery')->once()->andReturn(['provider_access_token' => 'first-token']);

        $secondAccessToken = \Mockery::mock(AccessTokenInterface::class);
        $secondAccessToken->shouldReceive('toQuery')->once()->andReturn(['provider_access_token' => 'second-token']);

        $firstResponse = new MockResponse('{}');
        $secondResponse = new MockResponse('{}');

        $app->setHttpClient(new MockHttpClient([$firstResponse, $secondResponse], 'https://qyapi.weixin.qq.com/'));
        $app->setProviderAccessToken($firstAccessToken);

        $firstClient = $app->getClient();
        $firstClient->request('GET', 'cgi-bin/service/get_provider_token');

        $app->setProviderAccessToken($secondAccessToken);

        $secondClient = $app->getClient();
        $secondClient->request('GET', 'cgi-bin/service/get_provider_token');

        $this->assertNotSame($firstClient, $secondClient);
        $this->assertSame(
            'https://qyapi.weixin.qq.com/cgi-bin/service/get_provider_token?provider_access_token=first-token',
            $firstResponse->getRequestUrl()
        );
        $this->assertSame(
            'https://qyapi.weixin.qq.com/cgi-bin/service/get_provider_token?provider_access_token=second-token',
            $secondResponse->getRequestUrl()
        );
    }

    public function test_get_authorizer_client_jsapi_ticket_and_oauth_helpers()
    {
        $app = new Application($this->createAppConfig());

        $suiteTicket = new SuiteTicket('suite-id', $app->getCache());
        $suiteTicket->setTicket('mock-suite-ticket');
        $app->setSuiteTicket($suiteTicket);

        $suiteAccessToken = \Mockery::mock(AccessTokenInterface::class);
        $suiteAccessToken->shouldReceive('getToken')->andReturn('mock-suite-access-token');

        $authorizerClient = $app->getAuthorizerClient('wx3cf0f39249000060', 'mock-permanent-code');
        $jsApiTicket = $app->getJsApiTicket('wx3cf0f39249000060', 'mock-permanent-code');
        $oauth = $app->getOAuth('suite-id', $suiteAccessToken);
        $corpOAuth = $app->getCorpOAuth('wx3cf0f39249000060', $suiteAccessToken);
        $authorizerAccessToken = $app->getAuthorizerAccessToken('wx3cf0f39249000060', 'mock-permanent-code');

        $this->assertInstanceOf(AccessTokenAwareClient::class, $authorizerClient);
        $this->assertInstanceOf(JsApiTicket::class, $jsApiTicket);
        $this->assertInstanceOf(OpenWeWork::class, $oauth);
        $this->assertInstanceOf(OpenWeWork::class, $corpOAuth);
        $this->assertInstanceOf(AuthorizerAccessToken::class, $authorizerAccessToken);
    }

    public function test_get_oauth_helpers_do_not_require_suite_ticket_or_access_token_up_front()
    {
        $app = new Application($this->createAppConfig());

        $oauth = $app->getOAuth('suite-id');
        $corpOAuth = $app->getCorpOAuth('wx3cf0f39249000060');

        $this->assertInstanceOf(OpenWeWork::class, $oauth);
        $this->assertInstanceOf(OpenWeWork::class, $corpOAuth);
    }

    /**
     * @return array<string, mixed>
     */
    private function createAppConfig(): array
    {
        return [
            'corp_id' => 'wx3cf0f39249000060',
            'provider_secret' => 'mock-provider-secret',
            'suite_id' => 'suite-id',
            'suite_secret' => 'mock-suite-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            'oauth' => [
                'redirect_url' => 'https://easywechat.com/callback',
            ],
        ];
    }
}
