<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Decorators;

use EasyWeChat\Kernel\Decorators\TerminateResult;
use EasyWeChat\Tests\TestCase;

class TerminateResultTest extends TestCase
{
    public function testGetContent()
    {
        $result = new TerminateResult('foo');

        $this->assertSame('foo', $result->content);
    }
}
