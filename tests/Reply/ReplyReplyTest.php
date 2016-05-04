<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Reply\Reply;

class ReplyReplyTest extends TestCase
{
    public function getReply()
    {
        $reply = Mockery::mock('EasyWeChat\Reply\Reply[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $reply->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
            ];
        });

        return $reply;
    }

    /**
     * Test current().
     */
    public function testCurrent()
    {
        $reply = $this->getReply();

        $response = $reply->current();
        $this->assertStringStartsWith(Reply::API_GET_CURRENT_SETTING, $response['api']);
        $this->assertEmpty($response['params']);
    }
}
