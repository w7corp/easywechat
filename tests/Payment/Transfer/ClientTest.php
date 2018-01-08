<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Transfer;

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Transfer\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * Make Application.
     *
     * @param array $config
     *
     * @return \EasyWeChat\Payment\Application
     */
    private function makeApp($config = [])
    {
        return new Application(array_merge([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-mcherant-id',
            'key' => 'foo-mcherant-key',
            'sub_appid' => 'foo-sub-appid',
            'sub_mch_id' => 'foo-sub-mch-id',
            'rsa_public_key_path' => \STUBS_ROOT.'/files/public-wx123456.pem',
        ], $config));
    }

    public function testQueryBalanceOrder()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['safeRequest'], $app)->makePartial();

        $partnerTradeNo = 'bar';

        $client->expects()->safeRequest('mmpaymkttransfers/gettransferinfo', \Mockery::on(function ($paramsForSafeRequest) use ($app) {
            $this->assertSame($paramsForSafeRequest['partner_trade_no'], 'bar');
            $this->assertSame($paramsForSafeRequest['appid'], $app['config']->app_id);
            $this->assertSame($paramsForSafeRequest['mch_id'], $app['config']->mch_id);

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryBalanceOrder($partnerTradeNo));
    }

    public function testToBalance()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['send', 'safeRequest'], $app)->makePartial();

        $params = [
            'foo' => 'bar',
        ];

        $client->expects()->safeRequest('mmpaymkttransfers/promotion/transfers', \Mockery::on(function ($paramsForSafeRequest) use ($params, $app) {
            $this->assertSame($params['foo'], $paramsForSafeRequest['foo']);
            $this->assertSame($paramsForSafeRequest['mchid'], $app['config']->mch_id);
            $this->assertSame($paramsForSafeRequest['mch_appid'], $app['config']->app_id);

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->toBalance($params));
    }

    public function testQueryBankCardOrder()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['safeRequest'], $app)->makePartial();

        $partnerTradeNo = 'bar';

        $client->expects()->safeRequest('mmpaysptrans/query_bank', \Mockery::on(function ($paramsForSafeRequest) use ($app) {
            $this->assertSame($paramsForSafeRequest['partner_trade_no'], 'bar');
            $this->assertSame($paramsForSafeRequest['mch_id'], $app['config']->mch_id);

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryBankCardOrder($partnerTradeNo));
    }

    public function testToBackCard()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['send', 'safeRequest'], $app)->makePartial();

        $params = [
            'partner_trade_no' => '1229222022',
            'enc_bank_no' => '6214830102234434',
            'enc_true_name' => '安正超',
            'bank_code' => '1001',
            'amount' => 100,
            'desc' => '测试',
        ];

        $client->expects()->safeRequest('mmpaysptrans/pay_bank', \Mockery::on(function ($paramsForSafeRequest) use ($params, $app) {
            $this->assertSame($params['partner_trade_no'], $paramsForSafeRequest['partner_trade_no']);
            $this->assertSame($params['bank_code'], $paramsForSafeRequest['bank_code']);
            $this->assertSame(100, $paramsForSafeRequest['amount']);

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->toBankCard($params));
    }
}
