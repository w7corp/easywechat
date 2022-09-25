<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OfficialAccount;

use EasyWeChat\OfficialAccount\Account;
use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use EasyWeChat\Tests\TestCase;

class AccountTest extends TestCase
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function test_application_can_create_account_instance()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
            ]
        );

        $this->assertInstanceOf(AccountInterface::class, $app->getAccount());
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function test_set_account_to_application()
    {
        $accountConfig = [
            'app_id' => 'wx3cf0f39249111111',
            'secret' => 'mock-account-secret',
            'token' => 'mock-account-token',
            'aes_key' => 'mock-account-aes-key',
        ];

        $account = new Account(
            appId: $accountConfig['app_id'],
            secret: $accountConfig['secret'],
            token: $accountConfig['token'],
            aesKey: $accountConfig['aes_key']
        );

        $applicationConfig = [
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
        ];

        $app = new Application(
            [
                'app_id' => $applicationConfig['app_id'],
                'secret' => $applicationConfig['secret'],
                'token' => $applicationConfig['token'],
            ]
        );

        $this->assertInstanceOf(AccountInterface::class, $app->getAccount());
        $this->assertSame($applicationConfig['app_id'], $app->getAccount()->getAppId());

        $app->setAccount($account);

        $this->assertNotSame($applicationConfig['app_id'], $app->getAccount()->getAppId());
        $this->assertSame($accountConfig['app_id'], $app->getAccount()->getAppId());
    }

    public function test_get_account_app_id()
    {
        $config = [
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes-key',
        ];

        $account = new Account(
            appId: $config['app_id'],
            secret: $config['secret'],
            token: $config['token'],
            aesKey: $config['aes_key']
        );

        $this->assertSame($config['app_id'], $account->getAppId());
    }

    public function test_get_account_secret()
    {
        $config = [
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes-key',
        ];

        $account = new Account(
            appId: $config['app_id'],
            secret: $config['secret'],
            token: $config['token'],
            aesKey: $config['aes_key']
        );

        $this->assertSame($config['secret'], $account->getSecret());
    }

    public function test_get_account_token()
    {
        $config = [
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes-key',
        ];

        $account = new Account(
            appId: $config['app_id'],
            secret: $config['secret'],
            token: $config['token'],
            aesKey: $config['aes_key']
        );

        $this->assertSame($config['token'], $account->getToken());
    }

    public function test_get_account_aes_key()
    {
        $config = [
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes-key',
        ];

        $account = new Account(
            appId: $config['app_id'],
            secret: $config['secret'],
            token: $config['token'],
            aesKey: $config['aes_key']
        );

        $this->assertSame($config['aes_key'], $account->getAesKey());
    }
}
