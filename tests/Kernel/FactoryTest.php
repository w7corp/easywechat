<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel;

use EasyWeChat\Factory;
use EasyWeChat\Tests\TestCase;

class FactoryTest extends TestCase
{
    public function testStaticCall()
    {
        $weWork = Factory::weWork([
            'client_id' => 'corpid@123',
            'client_secret' => 'corpsecret@123',
            'debug' => true,
        ]);

        $weWorkFromMake = Factory::make('weWork', [
            'debug' => true,
            'client_id' => 'corpid@123',
            'client_secret' => 'corpsecret@123',
        ]);

        $this->assertInstanceOf(\EasyWeChat\WeWork\Application::class, $weWork);
        $this->assertInstanceOf(\EasyWeChat\WeWork\Application::class, $weWorkFromMake);

        $expected = [
            'debug' => true,
            'client_id' => 'corpid@123',
            'client_secret' => 'corpsecret@123',
        ];
        $this->assertArraySubset($expected, $weWork['config']->all());
        $this->assertArraySubset($expected, $weWorkFromMake['config']->all());

        $this->assertInstanceOf(
            \EasyWeChat\OfficialAccount\Application::class,
            Factory::officialAccount(['appid' => 'appid@456'])
        );

        $this->assertInstanceOf(
            \EasyWeChat\OpenPlatform\Application::class,
            Factory::openPlatform(['appid' => 'appid@789'])
        );

        $this->assertInstanceOf(
            \EasyWeChat\MiniProgram\Application::class,
            Factory::miniProgram(['appid' => 'appid@789'])
        );

        $this->assertInstanceOf(
            \EasyWeChat\Payment\Application::class,
            Factory::payment(['appid' => 'appid@789'])
        );

        $this->assertInstanceOf(
            \EasyWeChat\BaseService\Application::class,
            Factory::baseService(['appid' => 'appid@789'])
        );
    }
}
