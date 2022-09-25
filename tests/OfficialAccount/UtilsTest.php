<?php

namespace EasyWeChat\Tests\OfficialAccount;

use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\OfficialAccount\JsApiTicket;
use EasyWeChat\OfficialAccount\Utils;
use EasyWeChat\Tests\TestCase;

class UtilsTest extends TestCase
{
    public function test_build_js_sdk_config()
    {
        $data = [
            'jsApiList' => ['api1', 'api2'],
            'openTagList' => ['openTag1', 'openTag2'],
            'debug' => true,
            'url' => 'https://www.easywechat.com/',
            'nonceStr' => 'mock-nonce',
            'timestamp' => 1601234567,
            'appId' => 'mock-appid',
            'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767',
        ];

        $signatue = [
            'url' => 'https://www.easywechat.com/',
            'nonceStr' => 'mock-nonce',
            'timestamp' => 1601234567,
            'appId' => 'mock-appid',
            'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767',
        ];

        $ticket = \Mockery::mock(JsApiTicket::class);
        $ticket->shouldReceive('configSignature')->andReturn($signatue);

        $app = \Mockery::mock(Application::class);
        $app->allows()->getTicket()->andReturn($ticket);

        $utils = new Utils($app);

        $result = $utils->buildJsSdkConfig('https://www.easywechat.com/', ['api1', 'api2'], ['openTag1', 'openTag2'], true);

        $this->assertSame($data, $result);
    }
}
