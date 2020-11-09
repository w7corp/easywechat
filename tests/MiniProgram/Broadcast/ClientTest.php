<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\Broadcast;

use EasyWeChat\MiniProgram\Broadcast\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'coverImgUrl' => 'foo',
            'name' => 'bar',
            'priceType' => 1,
            'price' => 1.0,
            'price2' => 2.0,
            'url' => 'pages/goods/index.html?id=10',
        ];
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/add', ['goodsInfo' => $params])->andReturn('mock-result');
        $response = $client->create($params);

        $this->assertSame('mock-result', $response);
    }

    public function testResetAudit()
    {
        $params = [
            'auditId' => '123456',
            'goodsId' => 1,
        ];
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/resetaudit', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->resetAudit('123456', 1));
    }

    public function testResubmitAudit()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/audit', ['goodsId' => 1])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->resubmitAudit(1));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/delete', ['goodsId' => 1])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete(1));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'coverImgUrl' => 'foo',
            'name' => 'bar',
            'priceType' => 1,
            'price' => 1.0,
            'price2' => 2.0,
            'url' => 'pages/goods/index.html?id=10',
            'goodsId' => 1,
        ];
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/update', ['goodsInfo' => $params])->andReturn('mock-result');
        $response = $client->update($params);

        $this->assertSame('mock-result', $response);
    }

    public function testGetGoodsWarehouse()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('wxa/business/getgoodswarehouse', ['goods_ids' => [1]])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getGoodsWarehouse([1]));
    }

    public function testGetApproved()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['offset' => 1, 'limit' => 30, 'status' => 1];
        $client->expects()->httpGet('wxaapi/broadcast/goods/getapproved', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getApproved($params));
    }

    public function testAddGoods()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['ids' => [9, 11], 'roomId' => 223];
        $client->expects()->httpPost('wxaapi/broadcast/room/addgoods', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->addGoods($params));
    }

    /**
     *  @author onekb <1@1kb.ren>
     */
    public function testGetRooms()
    {
        $client = $this->mockApiClient(\EasyWeChat\MiniProgram\Live\Client::class)->makePartial();

        $params = [
            'start' => 0,
            'limit' => 10,
        ];

        $client->expects()->httpPostJson('wxa/business/getliveinfo', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getRooms());
    }

    /**
     *  @author onekb <1@1kb.ren>
     */
    public function testGetPlaybacks()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $params = [
            'action' => 'get_replay',
            'room_id' => 1,
            'start' => 0,
            'limit' => 10,
        ];

        $client->expects()->httpPostJson('wxa/business/getliveinfo', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getPlaybacks(1));
    }

    public function testCreateLiveRoom()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'name' => "测试直播间", // 房间名字
            'coverImg' => "xxxxxx", // 填写mediaID，直播间背景图，图片规则：建议像素1080*1920，大小不超过2M
            'startTime' => 1588237130, // 直播计划开始时间，1.开播时间需在当前时间10min后，2.开始时间不能在6个月后
            'endTime' => 1588237130, // 直播计划结束时间，1.开播时间和结束时间间隔不得短于30min，不得超过12小时
            'anchorName' => "test1", // 主播昵称
            'anchorWechat' => "test1",  //主播微信号，需通过实名认证，否则将报错
            'shareImg' => "xxx", // 填写mediaID，直播间分享图，图片规则：建议像素800*640，大小不超过1M
            'type' => 1, // 直播类型，1：推流，0：手机直播
            'screenType' => 0, // 1：横屏，0：竖屏，自动根据实际视频分辨率调整
            'closeLike' => 0, // 1：关闭点赞 0：开启点赞 ，关闭后无法开启
            'closeGoods' => 0, // 1：关闭货架 0：打开货架，关闭后无法开启
            'closeComment' => 0 // 1：关闭评论 0：打开评论，关闭后无法开启
        ];

        $client->expects()->httpPost('wxaapi/broadcast/room/create', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->createLiveRoom($params));
    }

    public function testDeleteLiveRoom()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['id' => 6474];
        $client->expects()->httpPost('wxaapi/broadcast/room/deleteroom', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->deleteLiveRoom($params));
    }

    public function testUpdateLiveRoom()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'id' => 811,
            'name' => '测试更新副号1',
            'coverImg' => 'hw7zsntcr0rE-RBfBAaF553DqBk-J02UtWsP8VqrUh3tKu3jO_JwEO8n1cWTJ5TN',
            'startTime' => 1607443200,
            'endTime' => 1607450400,
            'anchorName' => '主播昵称11',
            'anchorWechat' => 'lintest1',
            'shareImg' => 'hw7zsntcr0rE-RBfBAaF553DqBk-J02UtWsP8VqrUh3tKu3jO_JwEO8n1cWTJ5TN',
            'closeLike' => 0,
            'closeGoods' => 0,
            'closeComment' => 0,
            'isFeedsPublic' => 0,
            'closeReplay' => 0,
            'closeShare' => 0,
            'closeKf' => 0,
            'feedsImg' => 'hw7zsntcr0rE-RBfBAaF553DqBk-J02UtWsP8VqrUh3tKu3jO_JwEO8n1cWTJ5TN'
        ];
        $client->expects()->httpPost('wxaapi/broadcast/room/editroom', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->updateLiveRoom($params));
    }

    public function testGetPushUrl()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474];
        $client->expects()->httpGet('wxaapi/broadcast/room/getpushurl', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getPushUrl($params));
    }

    public function testGetShareQrcode()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474, 'params' => '%7B%22foo%22%3A%22bar%22%7D'];
        $client->expects()->httpGet('wxaapi/broadcast/room/getsharedcode', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getShareQrcode($params));
    }

    public function testAddAssistant()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'roomId' => 6474,
            'users' => [
                ['username' => 'foo', 'nickname' => 'bar']
            ]
        ];
        $client->expects()->httpPost('wxaapi/broadcast/room/addassistant', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->addAssistant($params));
    }

    public function testUpdateAssistant()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474, 'username' => 'foo', 'nickname' => 'bar'];
        $client->expects()->httpPost('wxaapi/broadcast/room/modifyassistant', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->updateAssistant($params));
    }

    public function testDeleteAssistant()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474, 'username' => 'foo'];
        $client->expects()->httpPost('wxaapi/broadcast/room/removeassistant', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->deleteAssistant($params));
    }

    public function testGetAssistantList()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474];
        $client->expects()->httpGet('wxaapi/broadcast/room/getassistantlist', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getAssistantList($params));
    }

    public function testAddSubAnchor()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474, 'username' => 'foo'];
        $client->expects()->httpPost('wxaapi/broadcast/room/addsubanchor', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->addSubAnchor($params));
    }

    public function testUpdateSubAnchor()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474, 'username' => 'foo'];
        $client->expects()->httpPost('wxaapi/broadcast/room/modifysubanchor', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->updateSubAnchor($params));
    }

    public function testDeleteSubAnchor()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474];
        $client->expects()->httpPost('wxaapi/broadcast/room/deletesubanchor', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->deleteSubAnchor($params));
    }

    public function testGetSubAnchor()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474];
        $client->expects()->httpGet('wxaapi/broadcast/room/getsubanchor', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getSubAnchor($params));
    }

    public function testUpdateFeedPublic()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474, 'isFeedsPublic' => 1];
        $client->expects()->httpPost('wxaapi/broadcast/room/updatefeedpublic', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->updateFeedPublic($params));
    }

    public function testUpdateReplay()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474, 'closeReplay' => 1];
        $client->expects()->httpPost('wxaapi/broadcast/room/updatereplay', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->updateReplay($params));
    }

    public function testUpdateKf()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474, 'closeKf' => 1];
        $client->expects()->httpPost('wxaapi/broadcast/room/updatekf', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->updateKf($params));
    }

    public function testUpdateComment()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['roomId' => 6474, 'banComment' => 1];
        $client->expects()->httpPost('wxaapi/broadcast/room/updatecomment', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->updateComment($params));
    }

    public function testAddRole()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['role' => 1, 'username' => 'foo'];
        $client->expects()->httpPost('wxaapi/broadcast/role/addrole', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->addRole($params));
    }

    public function testDeleteRole()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['role' => 1, 'username' => 'foo'];
        $client->expects()->httpPost('wxaapi/broadcast/role/deleterole', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->deleteRole($params));
    }

    public function testGetRoleList()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'role' => 1,
            'offset' => 0,
            'limit' => 10,
            'keyword' => 'foo'
        ];
        $client->expects()->httpGet('wxaapi/broadcast/role/getrolelist', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getRoleList($params));
    }
}
