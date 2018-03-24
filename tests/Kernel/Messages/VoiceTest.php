<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Messages;

use EasyWeChat\Kernel\Messages\Voice;
use EasyWeChat\Tests\TestCase;

class VoiceTest extends TestCase
{
    public function testToXmlArray()
    {
        $message = new Voice('mock-media-id');

        $this->assertSame([
            'Voice' => [
                'MediaId' => 'mock-media-id',
            ],
        ], $message->toXmlArray());
    }
}
