<?php

namespace EasyWeChat\Tests\MiniProgram\Shop\Register;

use EasyWeChat\MiniProgram\Shop\Register\Client;
use EasyWeChat\Tests\TestCase;

/**
 * 自定义版交易组件开放接口
 *    申请接入接口
 *
 * Class ClientTest
 * @package EasyWeChat\Tests\MiniProgram\Shop\Register
 */
class ClientTest extends TestCase
{
    /**
     * 接入申请
     */
    public function testApply()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/register/apply')
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->apply());
    }

    /**
     * 获取接入状态
     */
    public function testCheck()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/register/check')
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->check());
    }

    /**
     * 完成接入任务
     */
    public function testFinishAccessInfo()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/register/finish_access_info', [
            'access_info_item' => 6
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->finishAccessInfo(6));
    }

    /**
     * 场景接入申请
     */
    public function testApplyScene()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/register/apply_scene', [
            'scene_group_id' => 1
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->applyScene());
    }
}
