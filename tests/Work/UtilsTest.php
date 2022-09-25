<?php

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Application;
use EasyWeChat\Work\JsApiTicket;
use EasyWeChat\Work\Utils;

class UtilsTest extends TestCase
{
    public function test_build_js_sdk_config()
    {
        $data = [
            'jsApiList' => ['api1', 'api2'],
            'openTagList' => ['openTag1', 'openTag2'],
            'debug' => true,
            'beta' => true,
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
        $ticket->shouldReceive('createConfigSignature')->andReturn($signatue);

        $app = \Mockery::mock(Application::class);
        $app->allows()->getTicket()->andReturn($ticket);

        $utils = new Utils($app);

        $result = $utils->buildJsSdkConfig('https://www.easywechat.com/', ['api1', 'api2'], ['openTag1', 'openTag2'], true, true);

        $this->assertSame($data, $result);
    }

    public function test_build_js_sdk_agent_config()
    {
        $data = [
            'jsApiList' => ['api1', 'api2'],
            'openTagList' => ['openTag1', 'openTag2'],
            'debug' => true,
            'url' => 'https://www.easywechat.com/',
            'nonceStr' => 'mock-nonce',
            'timestamp' => 1601234567,
            'corpid' => 'mock-corpid',
            'agentid' => 100001,
            'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767',
        ];

        $signatue = [
            'url' => 'https://www.easywechat.com/',
            'nonceStr' => 'mock-nonce',
            'timestamp' => 1601234567,
            'corpid' => 'mock-corpid',
            'agentid' => 100001,
            'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767',
        ];

        $ticket = \Mockery::mock(JsApiTicket::class);
        $ticket->shouldReceive('createAgentConfigSignature')->andReturn($signatue);

        $app = \Mockery::mock(Application::class);
        $app->allows()->getTicket()->andReturn($ticket);

        $utils = new Utils($app);

        $result = $utils->buildJsSdkAgentConfig(100001, 'https://www.easywechat.com/', ['api1', 'api2'], ['openTag1', 'openTag2'], true);

        $this->assertSame($data, $result);
    }
}
