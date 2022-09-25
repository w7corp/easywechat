<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Application;
use EasyWeChat\Work\Config;

class ConfigTest extends TestCase
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function test_application_created_can_get_config()
    {
        $app = new Application(
            [
                'corp_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
            ]
        );

        $this->assertInstanceOf(ConfigInterface::class, $app->getConfig());
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function test_set_config_to_application()
    {
        $config = [
            'corp_id' => 'wx3cf0f39249111111',
            'secret' => 'mock-account-secret',
            'token' => 'mock-account-token',
            'aes_key' => 'mock-account-aes-key',
            'agent_id' => 1000001,
        ];

        $config = new Config($config);

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

        $this->assertInstanceOf(ConfigInterface::class, $app->getConfig());
        $this->assertSame($applicationConfig['corp_id'], $app->getConfig()->get('corp_id'));
        $this->assertSame(false, $app->getConfig()->has('aes_key'));

        $app->setConfig($config);

        $this->assertNotSame($applicationConfig['corp_id'], $app->getConfig()->get('corp_id'));
        $this->assertSame($config['corp_id'], $app->getConfig()->get('corp_id'));
        $this->assertSame(true, $app->getConfig()->has('aes_key'));
    }

    public function test_init_config_can_check_missing_keys()
    {
        $config = [
            'secret' => 'mock-account-secret',
            'token' => 'mock-account-token',
            'aes_key' => 'mock-account-aes_key',
            'agent_id' => 1000001,
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('"%s" cannot be empty.', 'corp_id'));

        new Config($config);
    }
}
