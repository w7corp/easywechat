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
use Overtrue\Socialite\AccessToken;

class ClientTest extends TestCase
{
    public function testBridgeConfig()
    {
        $app = new Application([
            'app_id' => '123456',
            'merchant_id' => 'foo-merchant-id',
            'key' => 'key123456',
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
        $this->assertSame('123456', $config['appId']);
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
        $this->assertSame('123456', $config['appId']);
        $this->assertSame("prepay_id=$prepayId", $config['package']);
        $this->assertSame('MD5', $config['signType']);
    }

    public function testSdkConfig()
    {
        $app = new Application([
            'app_id' => '123456',
            'merchant_id' => 'foo-merchant-id',
            'key' => 'key123456',
        ]);

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
        $app = new Application([
            'app_id' => '123456',
            'merchant_id' => 'foo-merchant-id',
            'key' => 'key123456',
        ]);

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

        $this->assertSame($app['merchant']->app_id, $config['appid']);
        $this->assertSame($app['merchant']->merchant_id, $config['partnerid']);
        $this->assertSame($prepayId, $config['prepayid']);
        $this->assertSame('Sign=WXPay', $config['package']);
    }

    public function testShareAddressConfig()
    {
        $app = new Application([
            'app_id' => '123456',
            'merchant_id' => 'foo-merchant-id',
            'key' => 'key123456',
        ]);

        $client = $this->mockApiClient(Client::class, 'shareAddressConfig', $app)->makePartial();

        $accessTokenParams = ['access_token' => 'foo'];

        $mockAccessToken = \Mockery::mock(AccessToken::class.'[getToken]', [$accessTokenParams])->shouldAllowMockingProtectedMethods()->makePartial();
        $mockAccessToken->expects()->getToken()->andReturn('foo_access_token')->twice();

        // return json
        $config = json_decode($client->shareAddressConfig($mockAccessToken, true), true);
        $this->assertArrayHasKey('appId', $config);
        $this->assertArrayHasKey('scope', $config);
        $this->assertArrayHasKey('timeStamp', $config);
        $this->assertArrayHasKey('nonceStr', $config);
        $this->assertArrayHasKey('signType', $config);
        $this->assertArrayHasKey('addrSign', $config);
        $this->assertSame('123456', $config['appId']);
        $this->assertSame('jsapi_address', $config['scope']);
        $this->assertSame('SHA1', $config['signType']);

        // return array
        $config = $client->shareAddressConfig($mockAccessToken, false);
        $this->assertArrayHasKey('appId', $config);
        $this->assertArrayHasKey('scope', $config);
        $this->assertArrayHasKey('timeStamp', $config);
        $this->assertArrayHasKey('nonceStr', $config);
        $this->assertArrayHasKey('signType', $config);
        $this->assertArrayHasKey('addrSign', $config);
        $this->assertSame('123456', $config['appId']);
        $this->assertSame('jsapi_address', $config['scope']);
        $this->assertSame('SHA1', $config['signType']);
    }
}
