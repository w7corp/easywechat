<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OfficialAccount;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\OfficialAccount\AccessToken;
use EasyWeChat\OfficialAccount\Account;
use EasyWeChat\OfficialAccount\Account as AccountInterface;
use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\OfficialAccount\Contracts\Application as ApplicationInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\OfficialAccount\Server;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

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
        $server = new Server(\Mockery::mock(Account::class), \Mockery::mock(ServerRequestInterface::class));
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
}
