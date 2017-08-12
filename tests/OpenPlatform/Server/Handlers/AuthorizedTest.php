<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\OpenPlatform\Server\Handlers\Authorized;
use EasyWeChat\Tests\TestCase;

class AuthorizedTest extends TestCase
{
    public function testHandle()
    {
        $handler = new Authorized();

        $this->assertNull($handler->handle());
    }
}
