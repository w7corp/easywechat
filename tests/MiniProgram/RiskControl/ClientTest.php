<?php

namespace EasyWeChat\Tests\MiniProgram\RiskControl;

use EasyWeChat\MiniProgram\RiskControl\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{

    public function testGetUserRiskRank()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $client->expects()->httpPost('wxa/getuserriskrank', [
            'appid' => 'wx3cf0f39249eb0exx',
            'openid' => 'oahdg5c5ON6vtkUXLduLVKvzJzmM',
            'scene' => 0,
            'client_ip' => '175.171.38.165',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getUserRiskRank([
            'appid' => 'wx3cf0f39249eb0exx',
            'openid' => 'oahdg5c5ON6vtkUXLduLVKvzJzmM',
            'scene' => 0,
            'client_ip' => '175.171.38.165',
        ]));
    }
}