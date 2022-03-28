<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\ExternalContact;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\ExternalContact\GroupChatWayClient;

class GroupChatWayTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(GroupChatWayClient::class);

        $params = [
            "scene" => 2,
            "remark" => "aa_remark",
            "auto_create_room" => 1,
            "room_base_name" => "销售客服群",
            "room_base_id" => 10,
            "chat_id_list" => [
                "wrOgQhDgAAH2Yy-CTZ6POca8mlBEdaaa",
                "wrOgQhDgAALPUthpRAKvl7mgiQRwAAA"
            ],
            "state" => "klsdup3kj3s1"
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/groupchat/add_join_way', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create($params));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(GroupChatWayClient::class);

        $configId = '9ad7fa5cdaa6511298498f979c472aaa';
        $params = [
            'config_id' => $configId,
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/groupchat/get_join_way', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get($configId));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(GroupChatWayClient::class);

        $configId = '9ad7fa5cdaa6511298498f979c4722de';
        $params = [
            'config_id' => $configId,
            "scene" => 2,
            "remark" => "bb_remark",
            "auto_create_room" => 1,
            "room_base_name" => "销售客服群",
            "room_base_id" => 10,
            "chat_id_list" => [
                "wrOgQhDgAAH2Yy-CTZ6POca8mlBEdaaa",
                "wrOgQhDgAALPUthpRAKvl7mgiQRw_aaa"
            ],
            "state" => "klsdup3kj3s1"
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/groupchat/update_join_way', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update($configId, [
            "scene" => 2,
            "remark" => "bb_remark",
            "auto_create_room" => 1,
            "room_base_name" => "销售客服群",
            "room_base_id" => 10,
            "chat_id_list" => [
                "wrOgQhDgAAH2Yy-CTZ6POca8mlBEdaaa",
                "wrOgQhDgAALPUthpRAKvl7mgiQRw_aaa"
            ],
            "state" => "klsdup3kj3s1"
        ]));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(GroupChatWayClient::class);

        $configId = '42b34949e138eb6e027c123cba77faaa';
        $params = [
            'config_id' => $configId,
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/groupchat/del_join_way', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete($configId));
    }
}
