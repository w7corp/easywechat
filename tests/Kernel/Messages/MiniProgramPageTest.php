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

use EasyWeChat\Kernel\Messages\MiniProgramPage;
use EasyWeChat\Tests\TestCase;

class MiniProgramPageTest extends TestCase
{
    public function testBasicFeatures()
    {
        $msg = new MiniProgramPage([
            'title' => 'title',
            'appid' => 'minprogram appid',
            'pagepath' => 'miniprogram path',
            'thumb_media_id' => 'miniprogram cover',
        ]);

        $this->assertSame('title', $msg->get('title'));
        $this->assertSame('minprogram appid', $msg->get('appid'));
        $this->assertSame('miniprogram path', $msg->get('pagepath'));
        $this->assertSame('miniprogram cover', $msg->get('thumb_media_id'));

        $this->assertSame('miniprogrampage', $msg->getType());
        $msg->set('title', 'title1');

        $this->assertSame('title1', $msg->title);

        $msg->thumb_media_id = 'image2';

        $this->assertSame('image2', $msg->get('thumb_media_id'));
    }

    public function testTransformForJsonRequest()
    {
        $msg = new MiniProgramPage();
        $msg->title = 'title';
        $msg->appid = 'appid';
        $msg->thumb_media_id = 'image';
        $msg->pagepath = 'path';

        $this->assertSame(
            [
                'msgtype' => 'miniprogrampage',
                'miniprogrampage' => [
                    'title' => 'title',
                    'appid' => 'appid',
                    'thumb_media_id' => 'image',
                    'pagepath' => 'path',
                ],
            ],
            $msg->transformForJsonRequest()
        );
    }
}
