<?php

/*
* This file is part of the EasyWeChat.
*
* (c) overtrue <i@overtrue.me>
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

use EasyWeChat\Broadcast\Broadcast;

class BroadcastBroadcastTest extends PHPUnit_Framework_TestCase
{
    public function getBroadcast()
    {
        $broadcast = Mockery::mock('EasyWeChat\Broadcast\Broadcast[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $broadcast->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
                'quires' => empty($params[3]) ? null : $params[3],
            ];
        });

        return $broadcast;
    }

    /**
     * Test send().
     */
    public function testSend()
    {
        $broadcast = $this->getBroadcast();

        $response = $broadcast->send(Broadcast::MSG_TYPE_TEXT, 'CONTENT');

        $this->assertStringStartsWith(Broadcast::API_SEND_BY_GROUP, $response['api']);
        $data = [
            'filter' => [
                'is_to_all' => true,
            ],
            'text' => [
                'content' => 'CONTENT',
            ],
            'msgtype' => 'text',
        ];
        $this->assertEquals($data, $response['params']);

        $broadcast = $this->getBroadcast();

        $response = $broadcast->send(Broadcast::MSG_TYPE_TEXT, 'CONTENT', ['OPENID1', 'OPENID2', 'OPENID3']);

        $this->assertStringStartsWith(Broadcast::API_SEND_BY_OPENID, $response['api']);
        $data = [
            'touser' => [
                'OPENID1',
                'OPENID2',
                'OPENID3',
            ],
            'text' => [
                'content' => 'CONTENT',
            ],
            'msgtype' => 'text',
        ];
        $this->assertEquals($data, $response['params']);
    }

    /**
     * Test preview().
     */
    public function testPreview()
    {
        $broadcast = $this->getBroadcast();

        $response = $broadcast->preview(Broadcast::MSG_TYPE_TEXT, 'CONTENT', 'OPENID');

        $this->assertStringStartsWith(Broadcast::API_PREVIEW, $response['api']);
        $data = [
            Broadcast::PREVIEW_BY_OPENID => 'OPENID',
            'text' => [
                'content' => 'CONTENT',
            ],
            'msgtype' => 'text',
        ];
        $this->assertEquals($data, $response['params']);

        $broadcast = $this->getBroadcast();

        $response = $broadcast->preview(Broadcast::MSG_TYPE_TEXT, 'CONTENT', 'WXH', Broadcast::PREVIEW_BY_WXH);

        $this->assertStringStartsWith(Broadcast::API_PREVIEW, $response['api']);
        $data = [
            Broadcast::PREVIEW_BY_WXH => 'WXH',
            'text' => [
                'content' => 'CONTENT',
            ],
            'msgtype' => 'text',
        ];
        $this->assertEquals($data, $response['params']);
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        $broadcast = $this->getBroadcast();

        $response = $broadcast->delete('MSG_ID');

        $this->assertStringStartsWith(Broadcast::API_DELETE, $response['api']);
        $this->assertEquals('MSG_ID', $response['params']['msg_id']);
    }

    /**
     * Test status().
     */
    public function testStatus()
    {
        $broadcast = $this->getBroadcast();

        $response = $broadcast->status('MSG_ID');

        $this->assertStringStartsWith(Broadcast::API_GET, $response['api']);
        $this->assertEquals('MSG_ID', $response['params']['msg_id']);
    }
}
