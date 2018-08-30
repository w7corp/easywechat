<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Merchant;

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Merchant\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    protected function app()
    {
        return new Application([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-merchant-id',
            'notify_url' => 'http://easywechat.org/notify',
        ]);
    }

    public function testAddSubMerchant()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], $this->app());
        $client->expects()->safeRequest('secapi/mch/submchmanage', [
            'foo' => 'bar',
            'appid' => 'wx123456',
            'nonce_str' => '',
            'sub_mch_id' => '',
            'sub_appid' => '',
        ], 'post', ['query' => ['action' => 'add']])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->addSubMerchant(['foo' => 'bar']));
    }

    public function testQuerySubMerchantByMerchantId()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], $this->app());
        $client->expects()->safeRequest('secapi/mch/submchmanage', [
            'micro_mch_id' => 'foo-id',
            'appid' => 'wx123456',
            'nonce_str' => '',
            'sub_mch_id' => '',
            'sub_appid' => '',
        ], 'post', ['query' => ['action' => 'query']])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->querySubMerchantByMerchantId('foo-id'));
    }

    public function testQuerySubMerchantByWeChatId()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], $this->app());
        $client->expects()->safeRequest('secapi/mch/submchmanage', [
            'recipient_wechatid' => 'foo-id',
            'appid' => 'wx123456',
            'nonce_str' => '',
            'sub_mch_id' => '',
            'sub_appid' => '',
        ], 'post', ['query' => ['action' => 'query']])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->querySubMerchantByWeChatId('foo-id'));
    }
}
