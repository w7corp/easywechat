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

use EasyWeChat\Kernel\Messages\Music;
use EasyWeChat\Tests\TestCase;

class MusicTest extends TestCase
{
    public function testToXmlArray()
    {
        $message = new Music([
                'title' => '告白气球',
                'description' => '告白气球 - 周杰伦',
                'url' => 'http://easywechat.com/music/foo.mp3',
                'hq_url' => 'http://easywechat.com/music/foo_hq.mp3',
                'thumb_media_id' => 'Xhsbdaiu172j321kpsad711x76912ms2klas',
                'format' => 'mp3',
            ]);

        $this->assertSame([
            'Music' => [
                'Title' => '告白气球',
                'Description' => '告白气球 - 周杰伦',
                'MusicUrl' => 'http://easywechat.com/music/foo.mp3',
                'HQMusicUrl' => 'http://easywechat.com/music/foo_hq.mp3',
                'ThumbMediaId' => 'Xhsbdaiu172j321kpsad711x76912ms2klas',
            ],
        ], $message->toXmlArray());

        // without ThumbMediaId
        $message = new Music([
                'title' => '告白气球',
                'description' => '告白气球 - 周杰伦',
                'url' => 'http://easywechat.com/music/foo.mp3',
                'hq_url' => 'http://easywechat.com/music/foo_hq.mp3',
                'format' => 'mp3',
            ]);

        $this->assertSame([
            'Music' => [
                'Title' => '告白气球',
                'Description' => '告白气球 - 周杰伦',
                'MusicUrl' => 'http://easywechat.com/music/foo.mp3',
                'HQMusicUrl' => 'http://easywechat.com/music/foo_hq.mp3',
            ],
        ], $message->toXmlArray());

        $this->assertSame([
            'msgtype' => 'music',
            'music' => [
                'title' => '告白气球',
                'description' => '告白气球 - 周杰伦',
                'musicurl' => 'http://easywechat.com/music/foo.mp3',
                'hqmusicurl' => 'http://easywechat.com/music/foo_hq.mp3',
                'format' => 'mp3',
            ],
        ], $message->transformForJsonRequest());
    }
}
