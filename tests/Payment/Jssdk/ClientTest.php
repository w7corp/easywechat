<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Jssdk;

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Jssdk\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    private function makeApp($config = [])
    {
        return new Application(array_merge([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-mcherant-id',
            'key' => 'foo-mcherant-key',
            'sub_appid' => 'foo-sub-appid',
            'sub_mch_id' => 'foo-sub-mch-id',
        ], $config));
    }

    public function testBridgeConfig()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-mcherant-id',
            'key' => 'foo-mcherant-key',
            'sub_mch_id' => 'foo-sub-mch-id',
        ]);

        $client = $this->mockApiClient(Client::class, 'bridgeConfig', $app)->makePartial();

        $prepayId = 'foo';

        // return json
        $config = json_decode($client->bridgeConfig($prepayId, true), true);
        $this->assertArrayHasKey('appId', $config);
        $this->assertArrayHasKey('timeStamp', $config);
        $this->assertArrayHasKey('nonceStr', $config);
        $this->assertArrayHasKey('package', $config);
        $this->assertArrayHasKey('signType', $config);
        $this->assertArrayHasKey('paySign', $config);
        $this->assertSame($app['config']->app_id, $config['appId']);
        $this->assertSame("prepay_id=$prepayId", $config['package']);
        $this->assertSame('MD5', $config['signType']);

        // return array
        $config = $client->bridgeConfig($prepayId, false);
        $this->assertArrayHasKey('appId', $config);
        $this->assertArrayHasKey('timeStamp', $config);
        $this->assertArrayHasKey('nonceStr', $config);
        $this->assertArrayHasKey('package', $config);
        $this->assertArrayHasKey('signType', $config);
        $this->assertArrayHasKey('paySign', $config);
        $this->assertSame($app['config']->app_id, $config['appId']);
        $this->assertSame("prepay_id=$prepayId", $config['package']);
        $this->assertSame('MD5', $config['signType']);

        // sub_appid
        $app = new Application([
            'app_id' => 'wx123456',
            'merchant_id' => 'foo-mcherant-id',
            'key' => 'foo-mcherant-key',
            'sub_appid' => 'foo-sub-appid',
            'sub_mch_id' => 'foo-sub-mch-id',
        ]);
        $client = $this->mockApiClient(Client::class, 'bridgeConfig', $app)->makePartial();
        $config = $client->bridgeConfig($prepayId, false);
        $this->assertSame($app['config']->sub_appid, $config['appId']);
        $this->assertSame("prepay_id=$prepayId", $config['package']);
        $this->assertSame('MD5', $config['signType']);
    }

    public function testSdkConfig()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, 'bridgeConfig, sdkConfig', $app)->makePartial();

        $prepayId = 'foo';

        $client->expects()->bridgeConfig($prepayId, false)->andReturn(['foo' => 'bar', 'timeStamp' => '123'])->twice();

        $bridgeConfig = $client->bridgeConfig($prepayId, false);
        $sdkConfig = $client->sdkConfig($prepayId);

        $this->assertArrayHasKey('timestamp', $sdkConfig);
        $this->assertArrayNotHasKey('timeStamp', $sdkConfig);
        $this->assertSame($sdkConfig['timestamp'], $bridgeConfig['timeStamp']);
    }

    public function testAppConfig()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, 'appConfig', $app)->makePartial();

        $prepayId = 'foo';

        $config = $client->appConfig($prepayId);
        $this->assertArrayHasKey('appid', $config);
        $this->assertArrayHasKey('partnerid', $config);
        $this->assertArrayHasKey('prepayid', $config);
        $this->assertArrayHasKey('noncestr', $config);
        $this->assertArrayHasKey('timestamp', $config);
        $this->assertArrayHasKey('package', $config);
        $this->assertArrayHasKey('sign', $config);

        $this->assertSame($app['config']->app_id, $config['appid']);
        $this->assertSame($app['config']->mch_id, $config['partnerid']);
        $this->assertSame($prepayId, $config['prepayid']);
        $this->assertSame('Sign=WXPay', $config['package']);
    }

    public function testShareAddressConfig()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, 'shareAddressConfig', $app)->makePartial();

        $fakeAccessToken = 'foo_access_token';

        // return json
        $config = json_decode($client->setUrl('foo')->shareAddressConfig($fakeAccessToken, true), true);
        $this->assertArrayHasKey('appId', $config);
        $this->assertArrayHasKey('scope', $config);
        $this->assertArrayHasKey('timeStamp', $config);
        $this->assertArrayHasKey('nonceStr', $config);
        $this->assertArrayHasKey('signType', $config);
        $this->assertArrayHasKey('addrSign', $config);
        $this->assertSame($app['config']->app_id, $config['appId']);
        $this->assertSame('jsapi_address', $config['scope']);
        $this->assertSame('SHA1', $config['signType']);

        // return array
        $config = $client->shareAddressConfig($fakeAccessToken, false);
        $this->assertArrayHasKey('appId', $config);
        $this->assertArrayHasKey('scope', $config);
        $this->assertArrayHasKey('timeStamp', $config);
        $this->assertArrayHasKey('nonceStr', $config);
        $this->assertArrayHasKey('signType', $config);
        $this->assertArrayHasKey('addrSign', $config);
        $this->assertSame($app['config']->app_id, $config['appId']);
        $this->assertSame('jsapi_address', $config['scope']);
        $this->assertSame('SHA1', $config['signType']);
    }
}
