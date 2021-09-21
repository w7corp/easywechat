<?php

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Kernel\Client;
use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Work\AccessToken;
use EasyWeChat\Work\Server;
use EasyWeChat\Work\Account;
use EasyWeChat\Work\Account as AccountInterface;
use EasyWeChat\Work\Application;
use EasyWeChat\Work\Contracts\Application as ApplicationInterface;
use PHPUnit\Framework\TestCase;
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
        $encryptor = new Encryptor(appId: 'wx3cf0f39249000060', token: 'mock-token', aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');
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
        $server = new Server(\Mockery::mock(Account::class), \Mockery::mock(ServerRequestInterface::class));
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

        $this->assertInstanceOf(Client::class, $app->getClient());
        $this->assertSame($app->getClient(), $app->getClient());

        // set
        $client = new Client();
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
        $client = new Client();
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
}
