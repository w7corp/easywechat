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

use EasyWeChat\Applications\OfficialAccount\Message\Article;
use EasyWeChat\Tests\TestCase;

class MessageArticleTest extends TestCase
{
    /**
     * Test get attributes.
     */
    public function testAttributes()
    {
        $attributes = [
                'title' => 'TITLE',
                'thumb_media_id' => 'THUMB_MEDIA_ID',
                'author' => 'AUTHOR',
                'digest' => 'DIGEST',
                'show_cover_pic' => 'SHOW_COVER_PIC',
                'content' => 'CONTENT',
                'content_source_url' => 'CONTENT_SOURCE_URL',
            ];
        $article = new Article($attributes);

        $return = $article->only([
                    'title', 'thumb_media_id', 'author', 'digest',
                    'show_cover_pic', 'content', 'content_source_url',
                    ]);

        $this->assertSame($return, $attributes);

        $this->assertSame($article->show_cover, $attributes['show_cover_pic']);
        $this->assertSame($article->source_url, $attributes['content_source_url']);
    }
}
