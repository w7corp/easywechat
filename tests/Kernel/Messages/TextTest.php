<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Test\Kernel\Messages;

use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Tests\TestCase;

class TextTest extends TestCase
{
    public function testBasicFeatures()
    {
        $text = new Text('mock-content');

        $this->assertSame('mock-content', $text->content);
    }
}
