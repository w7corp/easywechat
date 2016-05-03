<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Broadcast\Broadcast;

class BroadcastBroadcastTest extends TestCase
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

        $response = $broadcast->preview(Broadcast::MSG_TYPE_TEXT, 'CONTENT', 'WXH', Broadcast::PREVIEW_BY_NAME);

        $this->assertStringStartsWith(Broadcast::API_PREVIEW, $response['api']);
        $data = [
            Broadcast::PREVIEW_BY_NAME => 'WXH',
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

    public function testAlias()
    {
        $broadcast = Mockery::mock(Broadcast::class.'[send,preview]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $broadcast->shouldReceive('send')->andReturnUsing(function ($api, $message, $to) {
            return compact('api', 'message', 'to');
        });

        $broadcast->shouldReceive('preview')->andReturnUsing(function ($msgType, $message, $to, $by) {
            return compact('msgType', 'message', 'to', 'by');
        });

        ////////// send /////////

        // sendText
        $result = $broadcast->sendText('hello', 'overtrue');
        $this->assertEquals(['api' => Broadcast::MSG_TYPE_TEXT, 'message' => 'hello', 'to' => 'overtrue'], $result);

        // sendNews
        $result = $broadcast->sendNews('hello', 'overtrue');
        $this->assertEquals(['api' => Broadcast::MSG_TYPE_NEWS, 'message' => 'hello', 'to' => 'overtrue'], $result);

        // sendVoice
        $result = $broadcast->sendVoice('hello', 'overtrue');
        $this->assertEquals(['api' => Broadcast::MSG_TYPE_VOICE, 'message' => 'hello', 'to' => 'overtrue'], $result);

        // sendImage
        $result = $broadcast->sendImage('hello', 'overtrue');
        $this->assertEquals(['api' => Broadcast::MSG_TYPE_IMAGE, 'message' => 'hello', 'to' => 'overtrue'], $result);

        // sendVideo
        $result = $broadcast->sendVideo('hello', 'overtrue');
        $this->assertEquals(['api' => Broadcast::MSG_TYPE_VIDEO, 'message' => 'hello', 'to' => 'overtrue'], $result);

        // sendCard
        $result = $broadcast->sendCard('hello', 'overtrue');
        $this->assertEquals(['api' => Broadcast::MSG_TYPE_CARD, 'message' => 'hello', 'to' => 'overtrue'], $result);

        ////////// preview /////////
        // previewText
        $result = $broadcast->previewText('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_TEXT, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_OPENID], $result);

        // previewNews
        $result = $broadcast->previewNews('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_NEWS, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_OPENID], $result);

        // previewVoice
        $result = $broadcast->previewVoice('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_VOICE, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_OPENID], $result);

        // previewImage
        $result = $broadcast->previewImage('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_IMAGE, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_OPENID], $result);

        // previewVideo
        $result = $broadcast->previewVideo('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_VIDEO, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_OPENID], $result);

        // previewCard
        $result = $broadcast->previewCard('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_CARD, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_OPENID], $result);

        // previewByName
        $result = $broadcast->previewByName(Broadcast::MSG_TYPE_TEXT, 'hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_TEXT, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_NAME], $result);

        // previewTextByName
        $result = $broadcast->previewTextByName('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_TEXT, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_NAME], $result);

        // previewNewsByName
        $result = $broadcast->previewNewsByName('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_NEWS, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_NAME], $result);

        // previewVoiceByName
        $result = $broadcast->previewVoiceByName('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_VOICE, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_NAME], $result);

        // previewImageByName
        $result = $broadcast->previewImageByName('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_IMAGE, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_NAME], $result);

        // previewVideoByName
        $result = $broadcast->previewVideoByName('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_VIDEO, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_NAME], $result);

        // previewCardByName
        $result = $broadcast->previewCardByName('hello', 'overtrue');
        $this->assertEquals(['msgType' => Broadcast::MSG_TYPE_CARD, 'message' => 'hello', 'to' => 'overtrue', 'by' => Broadcast::PREVIEW_BY_NAME], $result);
    }
}
