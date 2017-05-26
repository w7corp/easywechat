<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MediaPress;

use EasyWeChat\Tests\TestCase;

class MediaPressTest extends TestCase
{
    public function testOpenComment()
    {
        $result = $this->getMediaPress()->select('xxx123', 0)->openComment();

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/comment/open', $result['api']);
        $this->assertSame(['msg_data_id' => 'xxx123', 'index' => 0], $result['params']);
    }

    public function testCloseComment()
    {
        $result = $this->getMediaPress()->select('xxx123', 0)->closeComment();

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/comment/close', $result['api']);
        $this->assertSame(['msg_data_id' => 'xxx123', 'index' => 0], $result['params']);
    }

    public function testListComment()
    {
        $result = $this->getMediaPress()->select('xxx123', 0)->comments(10, 20, 0);

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/comment/list', $result['api']);
        $this->assertSame([
            'msg_data_id' => 'xxx123',
            'index' => 0,
            'begin' => 10,
            'count' => 20,
            'type' => 0,
        ], $result['params']);
    }

    public function testMarkElect()
    {
        $result = $this->getMediaPress()->select('xxx123', 0)->markElectComment('comment-id');

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/comment/markelect', $result['api']);
        $this->assertSame([
            'msg_data_id' => 'xxx123',
            'index' => 0,
            'user_comment_id' => 'comment-id',
        ], $result['params']);
    }

    public function testUnmarkElect()
    {
        $result = $this->getMediaPress()->select('xxx123', 0)->unmarkElectComment('comment-id');

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/comment/unmarkelect', $result['api']);
        $this->assertSame([
            'msg_data_id' => 'xxx123',
            'index' => 0,
            'user_comment_id' => 'comment-id',
        ], $result['params']);
    }

    public function testDeleteComment()
    {
        $result = $this->getMediaPress()->select('xxx123', 0)->deleteComment('comment-id');

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/comment/delete', $result['api']);
        $this->assertSame([
            'msg_data_id' => 'xxx123',
            'index' => 0,
            'user_comment_id' => 'comment-id',
        ], $result['params']);
    }

    public function testAddReply()
    {
        $result = $this->getMediaPress()->select('xxx123', 0)->reply('comment-id', 'content...');

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/comment/reply/add', $result['api']);
        $this->assertSame([
            'msg_data_id' => 'xxx123',
            'index' => 0,
            'user_comment_id' => 'comment-id',
            'content' => 'content...',
        ], $result['params']);
    }

    public function testDeleteReply()
    {
        $result = $this->getMediaPress()->select('xxx123', 0)->deleteReply('comment-id');

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/comment/reply/delete', $result['api']);
        $this->assertSame([
            'msg_data_id' => 'xxx123',
            'index' => 0,
            'user_comment_id' => 'comment-id',
        ], $result['params']);
    }

    private function getMediaPress()
    {
        $press = \Mockery::mock('EasyWeChat\MediaPress\MediaPress[parseJSON]', [\Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $press->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
                'quires' => empty($params[3]) ? null : $params[3],
            ];
        });

        return $press;
    }
}
