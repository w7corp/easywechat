<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\MiniApp;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\MiniApp\AccessToken;
use EasyWeChat\MiniApp\Account;
use EasyWeChat\MiniApp\Account as AccountInterface;
use EasyWeChat\MiniApp\Application;
use EasyWeChat\MiniApp\Contracts\Application as ApplicationInterface;
use EasyWeChat\MiniApp\Server;
use EasyWeChat\MiniApp\Utils;
use EasyWeChat\Tests\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ApplicationTest extends TestCase
{
    public function test_get_and_set_account()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
            ]
        );

        $this->assertInstanceOf(ApplicationInterface::class, $app);
        $this->assertInstanceOf(AccountInterface::class, $app->getAccount());
        $this->assertSame($app->getAccount(), $app->getAccount());

        // set
        $account = new Account(appId: 'wx3cf0f39249000060', secret: 'mock-secret', token: 'mock-token');
        $app->setAccount($account);
        $this->assertSame($account, $app->getAccount());
    }

    public function test_set_account_refreshes_default_access_token_client_and_server_dependencies()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
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
            'https://api.weixin.qq.com/'
        ));

        $firstAccessToken = $app->getAccessToken();
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

        $secondAccessToken = $app->getAccessToken();
        $this->assertSame('second-token', $secondAccessToken->getToken());

        $secondClient = $app->getClient();
        $secondClient->request('GET', 'cgi-bin/getcallbackip');

        $app->setRequest($this->createEncryptedXmlMessageRequest('<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[this is an updated test]]></Content>
            <MsgId>1234567890123456</MsgId>
        </xml>', $nextEncryptor));

        $secondServer = $app->getServer();
        $response = $secondServer
            ->addMessageListener('text', function () {
                return 'updated';
            })
            ->serve();

        $payload = Xml::parse((string) $response->getBody());
        $decrypted = Xml::parse($nextEncryptor->decrypt(
            $payload['Encrypt'],
            $payload['MsgSignature'],
            $payload['Nonce'],
            $payload['TimeStamp']
        ));

        $this->assertNotSame($firstAccessToken, $secondAccessToken);
        $this->assertNotSame($firstClient, $secondClient);
        $this->assertNotSame($firstServer, $secondServer);
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=second-token', $clientResponse->getRequestUrl());
        $this->assertSame('updated', $decrypted['Content']);
    }

    public function test_get_and_set_encryptor()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(Encryptor::class, $app->getEncryptor());
        $this->assertSame($app->getEncryptor(), $app->getEncryptor());

        // set
        $encryptor = new Encryptor(appId: 'wx3cf0f39249000060', token: 'mock-token', aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');
        $app->setEncryptor($encryptor);
        $this->assertSame($encryptor, $app->getEncryptor());
    }

    public function test_get_and_set_server()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(ServerInterface::class, $app->getServer());
        $this->assertSame($app->getServer(), $app->getServer());

        // set
        $server = new Server(\Mockery::mock(ServerRequestInterface::class));
        $app->setServer($server);
        $this->assertSame($server, $app->getServer());
    }

    public function test_get_and_set_access_token()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(AccessTokenInterface::class, $app->getAccessToken());

        // set
        $accessToken = new AccessToken('wx3cf0f39249000060', 'mock-secret');
        $app->setAccessToken($accessToken);
        $this->assertSame($accessToken, $app->getAccessToken());
    }

    public function test_set_account_preserves_custom_dependencies()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $encryptor = new Encryptor(
            appId: 'wx3cf0f39249000060',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        );
        $server = \Mockery::mock(ServerInterface::class);
        $accessToken = \Mockery::mock(AccessTokenInterface::class);
        $client = new AccessTokenAwareClient;

        $app->setEncryptor($encryptor);
        $app->setServer($server);
        $app->setAccessToken($accessToken);
        $app->setClient($client);

        $app->setAccount(new Account(
            appId: 'wx1234567890123456',
            secret: 'mock-secret-2',
            token: 'new-token',
            aesKey: 'bcdefghijklmnopqrstuvwxyz0123456789ABCDEFGH',
        ));

        $this->assertSame($encryptor, $app->getEncryptor());
        $this->assertSame($server, $app->getServer());
        $this->assertSame($accessToken, $app->getAccessToken());
        $this->assertSame($client, $app->getClient());
    }

    public function test_get_utils()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(Utils::class, $app->getUtils());
    }
}
