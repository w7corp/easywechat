<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\MiniApp\Application as MiniAppApplication;
use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use EasyWeChat\OpenPlatform\Account;
use EasyWeChat\OpenPlatform\Account as AccountInterface;
use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Authorization;
use EasyWeChat\OpenPlatform\AuthorizerAccessToken;
use EasyWeChat\OpenPlatform\ComponentAccessToken;
use EasyWeChat\OpenPlatform\Config;
use EasyWeChat\OpenPlatform\Contracts\Application as ApplicationInterface;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;
use EasyWeChat\OpenPlatform\Server;
use EasyWeChat\OpenPlatform\VerifyTicket;
use EasyWeChat\Tests\TestCase;
use Overtrue\Socialite\Providers\WeChat;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ApplicationTest extends TestCase
{
    public function test_get_and_set_account()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes_key',
        ]);

        $this->assertInstanceOf(ApplicationInterface::class, $app);
        $this->assertInstanceOf(AccountInterface::class, $app->getAccount());
        $this->assertSame($app->getAccount(), $app->getAccount());

        // set
        $account = new Account(appId: 'wx3cf0f39249000060', secret: 'mock-secret', token: 'mock-token', aesKey: 'mock-aes_key');
        $app->setAccount($account);
        $this->assertSame($account, $app->getAccount());
    }

    public function test_set_account_refreshes_default_dependencies()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $app->setCache(new Psr16Cache(new ArrayAdapter));

        $clientResponse = new MockResponse('{}');
        $app->setHttpClient(new MockHttpClient([
            new MockResponse(\json_encode([
                'component_access_token' => 'first-token',
                'expires_in' => 7200,
            ])),
            new MockResponse(\json_encode([
                'component_access_token' => 'second-token',
                'expires_in' => 7200,
            ])),
            $clientResponse,
        ], 'https://api.weixin.qq.com/'));

        $firstVerifyTicket = $app->getVerifyTicket();
        $firstVerifyTicket->setTicket('first-verify-ticket');

        $firstAccessToken = $app->getComponentAccessToken();
        $this->assertSame('first-token', $firstAccessToken->getToken());

        $firstClient = $app->getClient();
        $firstServer = $app->getServer();

        $nextAccount = new Account(
            appId: 'wx1234567890123456',
            secret: 'mock-secret-2',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        );
        $nextEncryptor = new Encryptor(
            appId: $nextAccount->getAppId(),
            token: $nextAccount->getToken(),
            aesKey: $nextAccount->getAesKey(),
        );

        $app->setAccount($nextAccount);

        $secondVerifyTicket = $app->getVerifyTicket();
        $secondVerifyTicket->setTicket('second-verify-ticket');

        $secondAccessToken = $app->getComponentAccessToken();
        $this->assertSame('second-token', $secondAccessToken->getToken());

        $secondClient = $app->getClient();
        $secondClient->request('GET', 'cgi-bin/component/api_get_authorizer_info');

        $app->setRequest($this->createEncryptedXmlMessageRequest('<xml>
            <AppId>wx1234567890123456</AppId>
            <CreateTime>1413192605</CreateTime>
            <InfoType>component_verify_ticket</InfoType>
            <ComponentVerifyTicket>persisted-verify-ticket</ComponentVerifyTicket>
        </xml>', $nextEncryptor));

        $secondServer = $app->getServer();
        $response = $secondServer->serve();

        $this->assertNotSame($firstVerifyTicket, $secondVerifyTicket);
        $this->assertNotSame($firstAccessToken, $secondAccessToken);
        $this->assertNotSame($firstClient, $secondClient);
        $this->assertNotSame($firstServer, $secondServer);
        $this->assertSame(
            'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=second-token',
            $clientResponse->getRequestUrl()
        );
        $this->assertSame('success', (string) $response->getBody());
        $this->assertSame('persisted-verify-ticket', $secondVerifyTicket->getTicket());
    }

    public function test_set_config_refreshes_default_dependencies()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $app->setCache(new Psr16Cache(new ArrayAdapter));

        $clientResponse = new MockResponse('{}');
        $app->setHttpClient(new MockHttpClient([
            new MockResponse(\json_encode([
                'component_access_token' => 'first-token',
                'expires_in' => 7200,
            ])),
            new MockResponse(\json_encode([
                'component_access_token' => 'second-token',
                'expires_in' => 7200,
            ])),
            $clientResponse,
        ], 'https://api.weixin.qq.com/'));

        $firstAccount = $app->getAccount();
        $firstVerifyTicket = $app->getVerifyTicket();
        $firstVerifyTicket->setTicket('first-verify-ticket');

        $firstAccessToken = $app->getComponentAccessToken();
        $this->assertSame('first-token', $firstAccessToken->getToken());

        $firstClient = $app->getClient();
        $firstServer = $app->getServer();

        $app->setConfig(new Config([
            'app_id' => 'wx1234567890123456',
            'secret' => 'mock-secret-2',
            'token' => 'new-token',
            'aes_key' => 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        ]));

        $nextEncryptor = new Encryptor(
            appId: 'wx1234567890123456',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        );

        $secondAccount = $app->getAccount();
        $secondVerifyTicket = $app->getVerifyTicket();
        $secondVerifyTicket->setTicket('second-verify-ticket');

        $secondAccessToken = $app->getComponentAccessToken();
        $this->assertSame('second-token', $secondAccessToken->getToken());

        $secondClient = $app->getClient();
        $secondClient->request('GET', 'cgi-bin/component/api_get_authorizer_info');

        $app->setRequest($this->createEncryptedXmlMessageRequest('<xml>
            <AppId>wx1234567890123456</AppId>
            <CreateTime>1413192605</CreateTime>
            <InfoType>component_verify_ticket</InfoType>
            <ComponentVerifyTicket>persisted-verify-ticket</ComponentVerifyTicket>
        </xml>', $nextEncryptor));

        $secondServer = $app->getServer();
        $response = $secondServer->serve();

        $this->assertNotSame($firstAccount, $secondAccount);
        $this->assertSame('wx1234567890123456', $secondAccount->getAppId());
        $this->assertNotSame($firstVerifyTicket, $secondVerifyTicket);
        $this->assertNotSame($firstAccessToken, $secondAccessToken);
        $this->assertNotSame($firstClient, $secondClient);
        $this->assertNotSame($firstServer, $secondServer);
        $this->assertSame(
            'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=second-token',
            $clientResponse->getRequestUrl()
        );
        $this->assertSame('success', (string) $response->getBody());
        $this->assertSame('persisted-verify-ticket', $secondVerifyTicket->getTicket());
    }

    public function test_get_and_set_encryptor()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(Encryptor::class, $app->getEncryptor());
        $this->assertSame($app->getEncryptor(), $app->getEncryptor());

        // set
        $encryptor = new Encryptor(
            appId: 'wx3cf0f39249000060',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG'
        );
        $app->setEncryptor($encryptor);
        $this->assertSame($encryptor, $app->getEncryptor());
    }

    public function test_get_and_set_server()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(ServerInterface::class, $app->getServer());
        $this->assertSame($app->getServer(), $app->getServer());

        // set
        $server = new Server(
            encryptor: \Mockery::mock(Encryptor::class),
            request: \Mockery::mock(ServerRequestInterface::class)
        );
        $app->setServer($server);
        $this->assertSame($server, $app->getServer());
    }

    public function test_get_and_set_component_access_token()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(AccessTokenInterface::class, $app->getAccessToken());
        // set
        $accessToken = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket());
        $app->setComponentAccessToken($accessToken);
        $this->assertSame($accessToken, $app->getAccessToken());
    }

    public function test_set_component_access_token_refreshes_resolved_client()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $firstAccessToken = \Mockery::mock(AccessTokenInterface::class);
        $firstAccessToken->shouldReceive('toQuery')->once()->andReturn(['component_access_token' => 'first-token']);

        $secondAccessToken = \Mockery::mock(AccessTokenInterface::class);
        $secondAccessToken->shouldReceive('toQuery')->once()->andReturn(['component_access_token' => 'second-token']);

        $firstResponse = new MockResponse('{}');
        $secondResponse = new MockResponse('{}');

        $app->setHttpClient(new MockHttpClient([$firstResponse, $secondResponse], 'https://api.weixin.qq.com/'));
        $app->setComponentAccessToken($firstAccessToken);

        $firstClient = $app->getClient();
        $firstClient->request('GET', 'cgi-bin/component/api_query_auth');

        $app->setComponentAccessToken($secondAccessToken);

        $secondClient = $app->getClient();
        $secondClient->request('GET', 'cgi-bin/component/api_query_auth');

        $this->assertNotSame($firstClient, $secondClient);
        $this->assertSame(
            'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=first-token',
            $firstResponse->getRequestUrl()
        );
        $this->assertSame(
            'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=second-token',
            $secondResponse->getRequestUrl()
        );
    }

    public function test_get_and_set_client()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(AccessTokenAwareClient::class, $app->getClient());
        $this->assertSame($app->getClient(), $app->getClient());

        $client = new AccessTokenAwareClient;
        $app->setClient($client);
        $this->assertSame($client, $app->getClient());
    }

    public function test_get_and_set_verify_ticket()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(VerifyTicketInterface::class, $app->getVerifyTicket());

        // set
        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $app->setVerifyTicket($verifyTicket);
        $this->assertSame($verifyTicket, $app->getVerifyTicket());
    }

    public function test_set_account_preserves_custom_dependencies()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $encryptor = new Encryptor(
            appId: 'wx3cf0f39249000060',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG'
        );
        $server = \Mockery::mock(ServerInterface::class);
        $accessToken = \Mockery::mock(AccessTokenInterface::class);
        $verifyTicket = new VerifyTicket('wx3cf0f39249000060', cache: $app->getCache());
        $client = new AccessTokenAwareClient;

        $app->setEncryptor($encryptor);
        $app->setServer($server);
        $app->setComponentAccessToken($accessToken);
        $app->setVerifyTicket($verifyTicket);
        $app->setClient($client);

        $app->setAccount(new Account(
            appId: 'wx1234567890123456',
            secret: 'mock-secret-2',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        ));

        $this->assertSame($encryptor, $app->getEncryptor());
        $this->assertSame($server, $app->getServer());
        $this->assertSame($accessToken, $app->getComponentAccessToken());
        $this->assertSame($verifyTicket, $app->getVerifyTicket());
        $this->assertSame($client, $app->getClient());
    }

    public function test_set_config_preserves_custom_account_and_dependencies()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $account = new Account(
            appId: 'wx9999999999999999',
            secret: 'custom-secret',
            token: 'custom-token',
            aesKey: 'mnopqrstuvwxyz0123456789ABCDEFGHabcdefghijk',
        );
        $encryptor = new Encryptor(
            appId: $account->getAppId(),
            token: $account->getToken(),
            aesKey: $account->getAesKey(),
        );
        $server = \Mockery::mock(ServerInterface::class);
        $accessToken = \Mockery::mock(AccessTokenInterface::class);
        $verifyTicket = new VerifyTicket($account->getAppId(), cache: $app->getCache());
        $client = new AccessTokenAwareClient;

        $app->setAccount($account);
        $app->setEncryptor($encryptor);
        $app->setServer($server);
        $app->setComponentAccessToken($accessToken);
        $app->setVerifyTicket($verifyTicket);
        $app->setClient($client);

        $app->setConfig(new Config([
            'app_id' => 'wx1234567890123456',
            'secret' => 'mock-secret-2',
            'token' => 'new-token',
            'aes_key' => 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        ]));

        $this->assertSame($account, $app->getAccount());
        $this->assertSame($encryptor, $app->getEncryptor());
        $this->assertSame($server, $app->getServer());
        $this->assertSame($accessToken, $app->getComponentAccessToken());
        $this->assertSame($verifyTicket, $app->getVerifyTicket());
        $this->assertSame($client, $app->getClient());
    }

    public function test_application_server_persists_verify_ticket_only_once()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $verifyTicket = \Mockery::mock(VerifyTicketInterface::class);
        $verifyTicket->shouldReceive('setTicket')->once()->with('persisted-verify-ticket')->andReturnSelf();
        $app->setVerifyTicket($verifyTicket);

        $this->assertInstanceOf(ServerInterface::class, $app->getServer());

        $request = $this->createEncryptedXmlMessageRequest('<xml>
            <AppId>some_appid</AppId>
            <CreateTime>1413192605</CreateTime>
            <InfoType>component_verify_ticket</InfoType>
            <ComponentVerifyTicket>persisted-verify-ticket</ComponentVerifyTicket>
        </xml>', $app->getEncryptor());

        $app->setRequest($request);

        $response = $app->getServer()->serve();

        $this->assertSame('success', (string) $response->getBody());
    }

    public function test_get_authorization()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $verifyTicket->setTicket('mock-verify-ticket');
        $app->setVerifyTicket($verifyTicket);
        $tokenResponse = [
            'component_access_token' => 'mock-access-token',
            'expires_in' => 2700,
        ];
        // token http client
        $mockTokenResponse = new MockResponse(\json_encode($tokenResponse));
        $tokenHttpClient = new MockHttpClient($mockTokenResponse, 'https://api.weixin.qq.com/');
        $token = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket(), httpClient: $tokenHttpClient);
        $app->setComponentAccessToken($token);
        $mockResponse = new MockResponse(
            \json_encode([
                'authorization_info' => ['authorizer_appid' => 'mock-appid'],
            ]),
            [
                'http_code' => 201,
                'response_headers' => ['Content-Type: application/json'],
            ]
        );
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);
        $this->assertInstanceOf(Authorization::class, $app->getAuthorization('mock-auth-code'));

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame(
            'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=mock-access-token',
            $mockResponse->getRequestUrl()
        );
        $this->assertSame(
            \json_encode([
                'component_appid' => 'wx3cf0f39249000060',
                'authorization_code' => 'mock-auth-code',
            ]),
            $mockResponse->getRequestOptions()['body']
        );
    }

    public function test_get_authorization_exception()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $verifyTicket->setTicket('mock-verify-ticket');
        $app->setVerifyTicket($verifyTicket);
        $tokenResponse = [
            'component_access_token' => 'mock-access-token',
            'expires_in' => 2700,
        ];
        // token http client
        $mockTokenResponse = new MockResponse(\json_encode($tokenResponse));
        $tokenHttpClient = new MockHttpClient($mockTokenResponse, 'https://api.weixin.qq.com/');
        $token = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket(), httpClient: $tokenHttpClient);
        $app->setComponentAccessToken($token);

        // exception
        $mockResponse = new MockResponse(
            \json_encode([
                'error_code' => 100029,
            ]),
            [
                'http_code' => 201,
                'response_headers' => ['Content-Type: application/json'],
            ]
        );
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Failed to get authorization_info: {"error_code":100029}');
        $app->getAuthorization('mock-auth-code');
    }

    public function test_refresh_authorizer_token()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $verifyTicket->setTicket('mock-verify-ticket');
        $app->setVerifyTicket($verifyTicket);

        $tokenResponse = [
            'component_access_token' => 'mock-access-token',
            'expires_in' => 2700,
        ];
        // token http client
        $mockTokenResponse = new MockResponse(\json_encode($tokenResponse));
        $tokenHttpClient = new MockHttpClient($mockTokenResponse, 'https://api.weixin.qq.com/');
        $token = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket(), httpClient: $tokenHttpClient);

        $app->setComponentAccessToken($token);
        $mockResponse = new MockResponse(
            \json_encode([
                'authorizer_access_token' => 'mock-access-token',
            ]),
            [
                'http_code' => 201,
                'response_headers' => ['Content-Type: application/json'],
            ]
        );
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);
        $this->assertSame([
            'authorizer_access_token' => 'mock-access-token',
        ], $app->refreshAuthorizerToken('mock-authorizer-appid', 'mock-refresh-token'));

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame(
            'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=mock-access-token',
            $mockResponse->getRequestUrl()
        );
        $this->assertSame(
            \json_encode([
                'component_appid' => 'wx3cf0f39249000060',
                'authorizer_appid' => 'mock-authorizer-appid',
                'authorizer_refresh_token' => 'mock-refresh-token',
            ]),
            $mockResponse->getRequestOptions()['body']
        );
    }

    public function test_refresh_authorizer_token_exception()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $verifyTicket->setTicket('mock-verify-ticket');
        $app->setVerifyTicket($verifyTicket);

        $tokenResponse = [
            'component_access_token' => 'mock-access-token',
            'expires_in' => 2700,
        ];
        // token http client
        $mockTokenResponse = new MockResponse(\json_encode($tokenResponse));
        $tokenHttpClient = new MockHttpClient($mockTokenResponse, 'https://api.weixin.qq.com/');

        $token = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket(), httpClient: $tokenHttpClient);

        $app->setComponentAccessToken($token);

        // exception
        $mockResponse = new MockResponse(
            \json_encode([
                'error_code' => 100029,
            ]),
            [
                'http_code' => 201,
                'response_headers' => ['Content-Type: application/json'],
            ]
        );
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Failed to get authorizer_access_token: {"error_code":100029}');
        $app->refreshAuthorizerToken('mock-authorizer-appid', 'mock-refresh-token');
    }

    public function test_get_oauth()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(WeChat::class, $app->getOAuth());
    }

    public function test_get_official_account()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(
            OfficialAccountApplication::class,
            $app->getOfficialAccount(new AuthorizerAccessToken('mock-app-id', 'mock-access-token'), [
                'secret' => 'mock-secret',
            ])
        );
    }

    public function test_get_official_account_uses_authorizer_app_encryptor_context()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);
        $app->setAccount(new Account(
            appId: 'wx1234567890123456',
            secret: 'mock-secret-2',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        ));

        $officialAccount = $app->getOfficialAccount(
            new AuthorizerAccessToken('wx8765432109876543', 'mock-access-token'),
            ['secret' => 'mock-authorizer-secret']
        );

        $this->assertSame('wx8765432109876543', $officialAccount->getAccount()->getAppId());
        $this->assertSame('new-token', $officialAccount->getConfig()->get('token'));
        $this->assertSame(
            'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
            $officialAccount->getConfig()->get('aes_key')
        );

        $authorizerEncryptor = new Encryptor(
            appId: 'wx8765432109876543',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        );

        $officialAccount->setRequest($this->createEncryptedXmlMessageRequest('<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[this is an authorizer test]]></Content>
            <MsgId>1234567890123456</MsgId>
        </xml>', $authorizerEncryptor));

        $response = $officialAccount->getServer()
            ->addMessageListener('text', function () {
                return 'authorizer-updated';
            })
            ->serve();

        $payload = Xml::parse((string) $response->getBody());
        $decrypted = Xml::parse($authorizerEncryptor->decrypt(
            $payload['Encrypt'],
            $payload['MsgSignature'],
            $payload['Nonce'],
            $payload['TimeStamp']
        ));

        $this->assertSame('authorizer-updated', $decrypted['Content']);
    }

    public function test_get_official_account_inherits_runtime_dependencies()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $cache = new Psr16Cache(new ArrayAdapter);
        $httpClient = new MockHttpClient([]);
        $logger = new NullLogger;

        $app->setCache($cache);
        $app->setHttpClient($httpClient);
        $app->setLogger($logger);

        $officialAccount = $app->getOfficialAccount(
            new AuthorizerAccessToken('wx8765432109876543', 'mock-access-token'),
            ['secret' => 'mock-authorizer-secret']
        );

        $this->assertSame($cache, $officialAccount->getCache());
        $this->assertSame($httpClient, $officialAccount->getHttpClient());
        $this->assertSame(
            $logger,
            (function () {
                return $this->logger;
            })->call($officialAccount)
        );
    }

    public function test_get_mini_app()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(
            MiniAppApplication::class,
            $app->getMiniApp(new AuthorizerAccessToken('mock-app-id', 'mock-access-token'), [
                'secret' => 'mock-secret',
            ])
        );
    }

    public function test_get_mini_app_uses_authorizer_app_encryptor_context()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);
        $app->setAccount(new Account(
            appId: 'wx1234567890123456',
            secret: 'mock-secret-2',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        ));

        $miniApp = $app->getMiniApp(
            new AuthorizerAccessToken('wx8765432109876543', 'mock-access-token'),
            ['secret' => 'mock-authorizer-secret']
        );

        $this->assertSame('wx8765432109876543', $miniApp->getAccount()->getAppId());
        $this->assertSame('new-token', $miniApp->getConfig()->get('token'));
        $this->assertSame(
            'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
            $miniApp->getConfig()->get('aes_key')
        );

        $authorizerEncryptor = new Encryptor(
            appId: 'wx8765432109876543',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        );

        $miniApp->setRequest($this->createEncryptedXmlMessageRequest('<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[this is an authorizer test]]></Content>
            <MsgId>1234567890123456</MsgId>
        </xml>', $authorizerEncryptor));

        $response = $miniApp->getServer()
            ->addMessageListener('text', function () {
                return 'authorizer-updated';
            })
            ->serve();

        $payload = Xml::parse((string) $response->getBody());
        $decrypted = Xml::parse($authorizerEncryptor->decrypt(
            $payload['Encrypt'],
            $payload['MsgSignature'],
            $payload['Nonce'],
            $payload['TimeStamp']
        ));

        $this->assertSame('authorizer-updated', $decrypted['Content']);
    }

    public function test_get_mini_app_inherits_runtime_dependencies()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $cache = new Psr16Cache(new ArrayAdapter);
        $httpClient = new MockHttpClient([]);
        $logger = new NullLogger;

        $app->setCache($cache);
        $app->setHttpClient($httpClient);
        $app->setLogger($logger);

        $miniApp = $app->getMiniApp(
            new AuthorizerAccessToken('wx8765432109876543', 'mock-access-token'),
            ['secret' => 'mock-authorizer-secret']
        );

        $this->assertSame($cache, $miniApp->getCache());
        $this->assertSame($httpClient, $miniApp->getHttpClient());
        $this->assertSame(
            $logger,
            (function () {
                return $this->logger;
            })->call($miniApp)
        );
    }
}
