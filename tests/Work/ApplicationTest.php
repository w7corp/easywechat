<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\AccessToken;
use EasyWeChat\Work\Account;
use EasyWeChat\Work\Application;
use EasyWeChat\Work\Contracts\Account as AccountInterface;
use EasyWeChat\Work\Contracts\Application as ApplicationInterface;
use EasyWeChat\Work\Encryptor;
use EasyWeChat\Work\JsApiTicket;
use EasyWeChat\Work\Server;
use EasyWeChat\Work\Utils;
use Overtrue\Socialite\Providers\WeWork;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApplicationTest extends TestCase
{
    public function test_get_and_set_account()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(ApplicationInterface::class, $app);
        $this->assertInstanceOf(AccountInterface::class, $app->getAccount());
        $this->assertSame($app->getAccount(), $app->getAccount());

        // set
        $account = new Account(corpId: 'wx3cf0f39249000060', secret: 'mock-secret', token: 'mock-token', aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');
        $app->setAccount($account);
        $this->assertSame($account, $app->getAccount());
    }

    public function test_set_account_refreshes_default_access_token_client_server_and_ticket_dependencies()
    {
        $app = new Application(
            [
                'corp_id' => 'wx5823bf96d3bd56c7',
                'secret' => 'mock-secret',
                'token' => 'QDG6eK',
                'aes_key' => 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C',
            ]
        );

        $app->setCache(new Psr16Cache(new ArrayAdapter));

        $tokenResponseA = new MockResponse(\json_encode([
            'access_token' => 'first-token',
            'expires_in' => 7200,
        ]));
        $tokenResponseB = new MockResponse(\json_encode([
            'access_token' => 'second-token',
            'expires_in' => 7200,
        ]));
        $clientResponse = new MockResponse('{}');

        $app->setHttpClient(new MockHttpClient(
            [$tokenResponseA, $tokenResponseB, $clientResponse],
            'https://qyapi.weixin.qq.com/'
        ));

        $firstAccessToken = $app->getAccessToken();
        $this->assertSame('first-token', $firstAccessToken->getToken());

        $firstClient = $app->getClient();
        $firstServer = $app->getServer();
        $firstTicket = $app->getTicket();

        $nextAccount = new Account(
            corpId: 'wx9876543210987654',
            secret: 'mock-secret-2',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        );
        $nextEncryptor = new Encryptor(
            corpId: $nextAccount->getCorpId(),
            token: $nextAccount->getToken(),
            aesKey: $nextAccount->getAesKey(),
        );

        $app->setAccount($nextAccount);

        $secondAccessToken = $app->getAccessToken();
        $this->assertSame('second-token', $secondAccessToken->getToken());

        $secondClient = $app->getClient();
        $secondClient->request('GET', 'cgi-bin/getcallbackip');

        $app->setRequest($this->createEncryptedXmlMessageRequest('<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[sys]]></FromUserName>
            <CreateTime>1403610513</CreateTime>
            <MsgType><![CDATA[event]]></MsgType>
            <Event><![CDATA[change_contact]]></Event>
            <ChangeType>change_contact</ChangeType>
            <UserID><![CDATA[zhangsan]]></UserID>
        </xml>', $nextEncryptor));

        $secondServer = $app->getServer();
        $response = $secondServer
            ->addMessageListener('event', function () {
                return 'updated';
            })
            ->serve();

        $message = Xml::parse((string) $response->getBody());
        $payload = Xml::parse($nextEncryptor->decrypt(
            $message['Encrypt'],
            $message['MsgSignature'],
            $message['Nonce'],
            $message['TimeStamp']
        ));

        $secondTicket = $app->getTicket();

        $this->assertNotSame($firstAccessToken, $secondAccessToken);
        $this->assertNotSame($firstClient, $secondClient);
        $this->assertNotSame($firstServer, $secondServer);
        $this->assertNotSame($firstTicket, $secondTicket);
        $this->assertSame('https://qyapi.weixin.qq.com/cgi-bin/getcallbackip?access_token=second-token', $clientResponse->getRequestUrl());
        $this->assertSame('updated', $payload['Content']);
    }

    public function test_get_and_set_encryptor()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(Encryptor::class, $app->getEncryptor());
        $this->assertSame($app->getEncryptor(), $app->getEncryptor());

        // set
        $encryptor = new Encryptor(corpId: 'wx3cf0f39249000060', token: 'mock-token', aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');
        $app->setEncryptor($encryptor);
        $this->assertSame($encryptor, $app->getEncryptor());
    }

    public function test_get_and_set_request()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(ServerRequestInterface::class, $app->getRequest());
        $this->assertSame($app->getRequest(), $app->getRequest());

        // set
        $request = \Mockery::mock(ServerRequestInterface::class);
        $app->setRequest($request);
        $this->assertSame($request, $app->getRequest());
    }

    public function test_get_and_set_server()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(ServerInterface::class, $app->getServer());
        $this->assertSame($app->getServer(), $app->getServer());

        // set
        $server = new Server(
            encryptor: $app->getEncryptor(),
            request: \Mockery::mock(ServerRequestInterface::class)
        );
        $app->setServer($server);
        $this->assertSame($server, $app->getServer());
    }

    public function test_get_server_honors_requested_message_type_after_server_is_cached()
    {
        $app = new Application(
            [
                'corp_id' => 'wx5823bf96d3bd56c7',
                'secret' => 'mock-secret',
                'token' => 'QDG6eK',
                'aes_key' => 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C',
            ]
        );

        $request = $this->createEncryptedXmlMessageRequest('<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[sys]]></FromUserName>
            <CreateTime>1403610513</CreateTime>
            <MsgType><![CDATA[event]]></MsgType>
            <Event><![CDATA[change_contact]]></Event>
            <ChangeType>change_contact</ChangeType>
            <UserID><![CDATA[zhangsan]]></UserID>
        </xml>', $app->getEncryptor());

        $app->setRequest($request);

        $this->assertInstanceOf(ServerInterface::class, $app->getServer());

        $response = $app->getServer(messageType: 'json')
            ->addMessageListener('event', function () {
                return [
                    'msgtype' => 'stream',
                    'stream' => [
                        'id' => 'id00001',
                        'finish' => true,
                        'content' => '信息已收到',
                    ],
                ];
            })
            ->serve();

        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));

        $payload = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('encrypt', $payload);
        $this->assertArrayHasKey('msgsignature', $payload);
    }

    public function test_application_server_uses_updated_request_after_server_is_resolved()
    {
        $app = new Application(
            [
                'corp_id' => 'wx5823bf96d3bd56c7',
                'secret' => 'mock-secret',
                'token' => 'QDG6eK',
                'aes_key' => 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C',
            ]
        );

        $this->assertInstanceOf(ServerInterface::class, $app->getServer());

        $request = $this->createEncryptedXmlMessageRequest('<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[sys]]></FromUserName>
            <CreateTime>1403610513</CreateTime>
            <MsgType><![CDATA[event]]></MsgType>
            <Event><![CDATA[change_contact]]></Event>
            <ChangeType>change_contact</ChangeType>
            <UserID><![CDATA[zhangsan]]></UserID>
        </xml>', $app->getEncryptor());

        $app->setRequest($request);

        $response = $app->getServer()
            ->addMessageListener('event', function () {
                return 'hello';
            })
            ->serve();

        $message = Xml::parse((string) $response->getBody());
        $payload = Xml::parse($app->getEncryptor()->decrypt(
            $message['Encrypt'],
            $message['MsgSignature'],
            $message['Nonce'],
            $message['TimeStamp']
        ));

        $this->assertSame('hello', $payload['Content']);
    }

    public function test_get_and_set_client()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(AccessTokenAwareClient::class, $app->getClient());
        $this->assertSame($app->getClient(), $app->getClient());

        // set
        $client = new AccessTokenAwareClient;
        $app->setClient($client);
        $this->assertSame($client, $app->getClient());
    }

    public function test_get_and_set_http_client()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(HttpClientInterface::class, $app->getHttpClient());
        $this->assertSame($app->getHttpClient(), $app->getHttpClient());

        // set
        $client = new AccessTokenAwareClient;
        $app->setHttpClient($client);
        $this->assertSame($client, $app->getHttpClient());
    }

    public function test_get_and_set_access_token()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(AccessTokenInterface::class, $app->getAccessToken());
        $this->assertSame($app->getAccessToken(), $app->getAccessToken());

        // set
        $accessToken = new AccessToken('wx3cf0f39249000060', 'mock-secret');
        $app->setAccessToken($accessToken);
        $this->assertSame($accessToken, $app->getAccessToken());
    }

    public function test_get_and_set_cache()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(CacheInterface::class, $app->getCache());
        $this->assertSame($app->getCache(), $app->getCache());

        // set
        $cache = \Mockery::mock(Psr16Cache::class);
        $app->setCache($cache);
        $this->assertSame($cache, $app->getCache());
    }

    public function test_get_and_set_config()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(ConfigInterface::class, $app->getConfig());
        $this->assertSame($app->getConfig(), $app->getConfig());

        // set
        $config = new Config(
            [
                'corp_id' => 'wx3cf0f39249000060-2',
                'secret' => 'mock-secret-2',
                'token' => 'mock-token-2',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );
        $app->setConfig($config);
        $this->assertSame($config, $app->getConfig());
    }

    public function test_get_and_set_ticket()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
                'agent_id' => 100001,
            ]
        );

        $this->assertInstanceOf(JsApiTicket::class, $app->getTicket());

        // set
        $ticket = new JsApiTicket('wx3cf0f39249000060', 'mock-token', $app->getCache(), $app->getClient());
        $app->setTicket($ticket);
        $this->assertSame($ticket, $app->getTicket());
    }

    public function test_set_account_preserves_custom_dependencies()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $encryptor = new Encryptor(
            corpId: 'wx3cf0f39249000060',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        );
        $server = \Mockery::mock(ServerInterface::class);
        $accessToken = \Mockery::mock(AccessTokenInterface::class);
        $ticket = new JsApiTicket('wx3cf0f39249000060', null, $app->getCache(), new MockHttpClient);
        $client = new AccessTokenAwareClient;

        $app->setEncryptor($encryptor);
        $app->setServer($server);
        $app->setAccessToken($accessToken);
        $app->setTicket($ticket);
        $app->setClient($client);

        $app->setAccount(new Account(
            corpId: 'wx9876543210987654',
            secret: 'mock-secret-2',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        ));

        $this->assertSame($encryptor, $app->getEncryptor());
        $this->assertSame($server, $app->getServer());
        $this->assertSame($accessToken, $app->getAccessToken());
        $this->assertSame($ticket, $app->getTicket());
        $this->assertSame($client, $app->getClient());
    }

    public function test_get_utils()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
                'agent_id' => 100001,
            ]
        );

        $this->assertInstanceOf(Utils::class, $app->getUtils());
    }

    public function test_get_oauth()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $oauth = $app->getOauth();
        $this->assertInstanceOf(WeWork::class, $oauth);
        $this->assertNull($this->getOAuthAgentId($oauth));

        // with default agent id
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
                'agent_id' => '100001',
            ]
        );

        $oauth = $app->getOauth();
        $this->assertInstanceOf(WeWork::class, $oauth);
        $this->assertSame(100001, $this->getOAuthAgentId($oauth));
    }

    public function test_get_oauth_does_not_eagerly_resolve_access_token()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $accessToken = \Mockery::mock(AccessTokenInterface::class);
        $accessToken->shouldNotReceive('getToken');
        $app->setAccessToken($accessToken);

        $oauth = $app->getOauth();

        $this->assertInstanceOf(WeWork::class, $oauth);
    }

    private function getOAuthAgentId(WeWork $oauth): ?int
    {
        /** @var int|null $agentId */
        $agentId = \Closure::bind(function (): ?int {
            return $this->agentId;
        }, $oauth, $oauth)();

        return $agentId;
    }
}
