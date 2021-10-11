<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use PHPUnit\Framework\TestCase;

class InteractWithConfigTest extends TestCase
{
    public function test_get_and_set_config()
    {
        $app = new DummyClassForInteractWithConfigTest([]);

        $this->assertInstanceOf(ConfigInterface::class, $app->getConfig());
        $this->assertSame($app->getConfig(), $app->getConfig());

        // set
        $config = \Mockery::mock(ConfigInterface::class);
        $app->setConfig($config);
        $this->assertSame($config, $app->getConfig());
    }
}

class DummyClassForInteractWithConfigTest
{
    use InteractWithConfig;
}
