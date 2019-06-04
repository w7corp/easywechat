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
use EasyWeChat\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testProperties()
    {
        $app = new Application([
            'mch_id' => 'foo-merchant-id',
        ]);
        $this->assertInstanceOf(\EasyWeChat\MicroMerchant\Certficates\Client::class, $app->certficates);
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
}
