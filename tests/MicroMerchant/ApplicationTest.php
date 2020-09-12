<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MicroMerchant;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException;
use EasyWeChat\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testProperties()
    {
        $app = new Application([
            'mch_id' => 'foo-merchant-id',
        ]);
        $this->assertInstanceOf(\EasyWeChat\MicroMerchant\Certficates\Client::class, $app->certficates);
        $this->assertInstanceOf(\EasyWeChat\MicroMerchant\Base\Client::class, $app->base);
        $this->assertInstanceOf(\EasyWeChat\MicroMerchant\Material\Client::class, $app->material);
        $this->assertInstanceOf(\EasyWeChat\MicroMerchant\MerchantConfig\Client::class, $app->merchantConfig);
        $this->assertInstanceOf(\EasyWeChat\MicroMerchant\Withdraw\Client::class, $app->withdraw);
    }

    public function testGetKey()
    {
        $app = new Application(['key' => '88888888888888888888888888888888']);
        $this->assertSame('88888888888888888888888888888888', $app->getKey());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf("'%s' should be 32 chars length.", '1234'));
        $app = new Application(['key' => '1234']);
        $app->getKey();
    }

    public function testSetSubMchId()
    {
        $app = new Application(['key' => '88888888888888888888888888888888']);
        $this->assertSame($app, $app->setSubMchId('sub_mch_id', 'appid'));
        $this->assertSame('sub_mch_id', $app->config->sub_mch_id);
        $this->assertSame('appid', $app->config->appid);
    }

    public function testVerifySignature()
    {
        $app = new Application(['key' => '88888888888888888888888888888888']);

        $this->assertTrue($app->verifySignature([
            'foo' => 'bar',
            'sign' => 'A17558A18A547578818256FD52E74BB3EA9475E021F5F210809F2B7916C53B1D',
        ]));

        $this->assertTrue($app->verifySignature([
            'foo' => 'bar',
            'sign' => '834A25C9A5B48305AB997C9A7E101530',
        ]));

        $this->assertFalse($app->verifySignature([
            'foo' => 'bar',
        ]));
        $this->expectException(InvalidSignException::class);
        $this->expectExceptionMessage('return value signature verification error');
        $this->assertTrue($app->verifySignature([
            'foo' => 'bar',
            'sign' => '834A25C9A5B48305AB997C9A7E101531',
        ]));
    }
}
