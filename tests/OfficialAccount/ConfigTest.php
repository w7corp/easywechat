<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OfficialAccount;

use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\OfficialAccount\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function test_application_created_can_get_config()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
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
            'app_id' => 'wx3cf0f39249111111',
            'secret' => 'mock-account-secret',
            'token' => 'mock-account-token',
            'aes_key' => 'mock-account-aes-key',
        ];

        $config = new Config($config);

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

        $this->assertInstanceOf(ConfigInterface::class, $app->getConfig());
        $this->assertSame($applicationConfig['app_id'], $app->getConfig()->get('app_id'));
        $this->assertSame(false, $app->getConfig()->has('aes_key'));

        $app->setConfig($config);

        $this->assertNotSame($applicationConfig['app_id'], $app->getConfig()->get('app_id'));
        $this->assertSame($config['app_id'], $app->getConfig()->get('app_id'));
        $this->assertSame(true, $app->getConfig()->has('aes_key'));
    }

    public function test_init_config_can_check_missing_keys()
    {
        $config = [
            'app_id' => 'wx3cf0f39249111111',
            'secret' => 'mock-account-secret',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('"%s" cannot be empty.', 'aes_key'));

        new Config($config);
    }
}
