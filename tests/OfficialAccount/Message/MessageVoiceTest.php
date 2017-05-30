<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Message;

use EasyWeChat\Applications\OfficialAccount\Message\Voice;
use EasyWeChat\Tests\TestCase;

class MessageVoiceTest extends TestCase
{
    /**
     * Test media().
     */
    public function testMedia()
    {
        $voice = new Voice();

        $return = $voice->media('foobar');

        $this->assertEquals($voice, $return);
        $this->assertEquals('foobar', $return->media_id);
    }
}
