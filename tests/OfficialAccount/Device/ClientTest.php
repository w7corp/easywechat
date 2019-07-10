<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Device;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OfficialAccount\Device\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function getClient($config = [])
    {
        $app = new ServiceContainer(array_merge([
            'device_type' => 'mock-type',
        ], $config));

        return $this->mockApiClient(Client::class, [], $app);
    }

    public function testMessage()
    {
        $client = $this->getClient();

        $client->expects()->httpPostJson('device/transmsg', [
            'device_type' => 'mock-type',
            'device_id' => 'mock-id',
            'open_id' => 'mock-openid',
            'content' => base64_encode('hello'),
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->message('mock-id', 'mock-openid', 'hello'));
    }

    public function testQrCode()
    {
        $client = $this->getClient();

        $client->expects()->httpPostJson('device/create_qrcode', [
            'device_num' => 2,
            'device_id_list' => ['mock-id1', 'mock-id2'],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->qrCode(['mock-id1', 'mock-id2']));
    }

    public function testCreateId()
    {
        $client = $this->getClient();

        $client->expects()->httpGet('device/getqrcode', [
            'product_id' => 'mock-pid',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->createId('mock-pid'));
    }

    public function testBind()
    {
        $client = $this->getClient();

        $client->expects()->httpPostJson('device/bind', [
            'ticket' => 'mock-ticket',
            'device_id' => 'mock-id',
            'openid' => 'mock-openid',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->bind('mock-openid', 'mock-id', 'mock-ticket'));
    }

    public function testUnbind()
    {
        $client = $this->getClient();

        $client->expects()->httpPostJson('device/unbind', [
            'ticket' => 'mock-ticket',
            'device_id' => 'mock-id',
            'openid' => 'mock-openid',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->unbind('mock-openid', 'mock-id', 'mock-ticket'));
    }

    public function testForceBind()
    {
        $client = $this->getClient();

        $client->expects()->httpPostJson('device/compel_bind', [
            'device_id' => 'mock-id',
            'openid' => 'mock-openid',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->forceBind('mock-openid', 'mock-id'));
    }

    public function testForceUnbind()
    {
        $client = $this->getClient();

        $client->expects()->httpPostJson('device/compel_unbind', [
            'device_id' => 'mock-id',
            'openid' => 'mock-openid',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->forceUnbind('mock-openid', 'mock-id'));
    }

    public function testStatus()
    {
        $client = $this->getClient();

        $client->expects()->httpGet('device/get_stat', [
            'device_id' => 'mock-id',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->status('mock-id'));
    }

    public function testVerify()
    {
        $client = $this->getClient();

        $client->expects()->httpPost('device/verify_qrcode', [
            'ticket' => 'mock-ticket',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->verify('mock-ticket'));
    }

    public function testOpenid()
    {
        $client = $this->getClient();

        $client->expects()->httpGet('device/get_openid', [
            'device_id' => 'mock-id',
            'device_type' => 'mock-type',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->openid('mock-id'));
    }

    public function testListByOpenid()
    {
        $client = $this->getClient();

        $client->expects()->httpGet('device/get_bind_device', [
            'openid' => 'mock-openid',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->listByOpenid('mock-openid'));
    }

    public function testAuthorize()
    {
        $devices = [
            [
                'id' => 'mock-id1',
                'mac' => 'mock-mac',
                'connect_protocol' => 'mock-connect_protocol',
                'auth_key' => 'mock-auth_key',
                'close_strategy' => 'mock-close_strategy',
                'conn_strategy' => 'mock-conn_strategy',
                'crypt_method' => 'mock-crypt_method',
                'auth_ver' => 'mock-auth_ver',
                'manu_mac_pos' => 'mock-manu_mac_pos',
                'ser_mac_pos' => 'mock-ser_mac_pos',
                'ble_simple_protocol' => 'mock-ble_simple_protocol',
            ],
            [
                'id' => 'mock-id2',
                'mac' => 'mock-mac',
                'connect_protocol' => 'mock-connect_protocol',
                'auth_key' => 'mock-auth_key',
                'close_strategy' => 'mock-close_strategy',
                'conn_strategy' => 'mock-conn_strategy',
                'crypt_method' => 'mock-crypt_method',
                'auth_ver' => 'mock-auth_ver',
                'manu_mac_pos' => 'mock-manu_mac_pos',
                'ser_mac_pos' => 'mock-ser_mac_pos',
                'ble_simple_protocol' => 'mock-ble_simple_protocol',
            ],
        ];

        $client = $this->getClient();

        $client->expects()->httpPostJson('device/authorize_device', [
            'device_num' => 2,
            'device_list' => $devices,
            'op_type' => 0,
            'product_id' => 'mock-pid',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->authorize($devices, 'mock-pid'));
    }
}
