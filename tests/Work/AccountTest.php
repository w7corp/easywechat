<?php

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Work\Account;
use EasyWeChat\Work\Contracts\Account as AccountInterface;
use EasyWeChat\Work\Application;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function test_application_created_can_get_account()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'mock-aes_key',
                'agent_id' => 1000001,
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
            'corp_id' => 'wx3cf0f39249111111',
            'secret' => 'mock-account-secret',
            'token' => 'mock-account-token',
            'aes_key' => 'mock-account-aes-key',
            'agent_id' => 1000001,
        ];

        $account = new Account(
            corpId: $accountConfig['corp_id'],
            secret: $accountConfig['secret'],
            token: $accountConfig['token'],
            aesKey: $accountConfig['aes_key'],
            agentId: $accountConfig['agent_id']
        );

        $applicationConfig = [
            'corp_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
        ];

        $app = new Application(
            [
                'corp_id' => $applicationConfig['corp_id'],
                'secret' => $applicationConfig['secret'],
                'token' => $applicationConfig['token'],
            ]
        );

        $this->assertInstanceOf(AccountInterface::class, $app->getAccount());
        $this->assertSame($applicationConfig['corp_id'], $app->getAccount()->getCorpId());

        $app->setAccount($account);

        $this->assertNotSame($applicationConfig['corp_id'], $app->getAccount()->getCorpId());
        $this->assertSame($accountConfig['corp_id'], $app->getAccount()->getCorpId());
    }

    public function test_get_account_corp_id()
    {
        $config = [
            'corp_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes-key',
        ];

        $account = new Account(
            corpId: $config['corp_id'],
            secret: $config['secret'],
            token: $config['token'],
            aesKey: $config['aes_key']
        );

        $this->assertSame($config['corp_id'], $account->getCorpId());
    }

    public function test_get_account_secret()
    {
        $config = [
            'corp_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes-key',
        ];

        $account = new Account(
            corpId: $config['corp_id'],
            secret: $config['secret'],
            token: $config['token'],
            aesKey: $config['aes_key']
        );

        $this->assertSame($config['secret'], $account->getSecret());
    }

    public function test_get_account_token()
    {
        $config = [
            'corp_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes-key',
        ];

        $account = new Account(
            corpId: $config['corp_id'],
            secret: $config['secret'],
            token: $config['token'],
            aesKey: $config['aes_key']
        );

        $this->assertSame($config['token'], $account->getToken());
    }

    public function test_get_account_aes_key()
    {
        $config = [
            'corp_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes-key',
        ];

        $account = new Account(
            corpId: $config['corp_id'],
            secret: $config['secret'],
            token: $config['token'],
            aesKey: $config['aes_key']
        );

        $this->assertSame($config['aes_key'], $account->getAesKey());
    }
}
