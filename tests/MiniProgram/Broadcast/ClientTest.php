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
}
