<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\AccessToken;
use EasyWeChat\Work\Account;
use EasyWeChat\Work\Contracts\Account as AccountInterface;
use EasyWeChat\Work\Application;
use EasyWeChat\Work\Contracts\Application as ApplicationInterface;
use EasyWeChat\Work\Encryptor;
use EasyWeChat\Work\JsApiTicket;
use EasyWeChat\Work\Server;
use EasyWeChat\Work\Utils;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Psr16Cache;
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
        $account = new Account(corpId: 'wx3cf0f39249000060', secret: 'mock-secret', token: 'mock-token', aesKey:'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');
        $app->setAccount($account);
        $this->assertSame($account, $app->getAccount());
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
        $client = new AccessTokenAwareClient();
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
        $client = new AccessTokenAwareClient();
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
}
