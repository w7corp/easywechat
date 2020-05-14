<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\ProfitSharing;

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\ProfitSharing\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    protected function app()
    {
        return new Application([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-merchant-id',
        ]);
    }

    public function testAddReceiver()
    {
        $client = $this->mockApiClient(
            Client::class,
            ['request'],
            $this->app()
        );

        $client->expects()->request(
            'pay/profitsharingaddreceiver',
            [
                'appid' => 'wx123456',
                'receiver' => '{"type":"MERCHANT_ID","account":"190001001","name":"实例商户全称"}',
            ]
        )->andReturn('mock-result');

        $this->assertSame('mock-result', $client->addReceiver([
            'type' => 'MERCHANT_ID',
            'account' => '190001001',
            'name' => '实例商户全称',
        ]));
    }

    public function testDeleteReceiver()
    {
        $client = $this->mockApiClient(
            Client::class,
            ['request'],
            $this->app()
        );

        $client->expects()->request(
            'pay/profitsharingremovereceiver',
            [
                'appid' => 'wx123456',
                'receiver' => '{"type":"MERCHANT_ID","account":"190001001","name":"实例商户全称"}',
            ]
        )->andReturn('mock-result');

        $this->assertSame(
            'mock-result',
            $client->deleteReceiver([
                'type' => 'MERCHANT_ID',
                'account' => '190001001',
                'name' => '实例商户全称',
            ])
        );
    }

    public function testShare()
    {
        $client = $this->mockApiClient(
            Client::class,
            ['safeRequest'],
            $this->app()
        );

        $client->expects()->safeRequest(
            'secapi/pay/profitsharing',
            [
                'appid' => 'wx123456',
                'transaction_id' => '4208450740201411110007820472',
                'out_order_no' => 'P20150806125346',
                'receivers' => '[{"type":"MERCHANT_ID","account":"190001001","amount":100,"description":"分到商户"},{"type":"PERSONAL_WECHATID","account":"86693952","amount":888,"description":"分到个人"}]',
            ]
        )->andReturn('mock-result');

        $this->assertSame('mock-result', $client->share(
            '4208450740201411110007820472',
            'P20150806125346',
            [[
                'type' => 'MERCHANT_ID',
                'account' => '190001001',
                'amount' => 100,
                'description' => '分到商户',
            ], [
                'type' => 'PERSONAL_WECHATID',
                'account' => '86693952',
                'amount' => 888,
                'description' => '分到个人',
            ]]
        ));
    }

    public function testMultiShare()
    {
        $client = $this->mockApiClient(
            Client::class,
            ['safeRequest'],
            $this->app()
        );

        $client->expects()->safeRequest(
            'secapi/pay/multiprofitsharing',
            [
                'appid' => 'wx123456',
                'transaction_id' => '4208450740201411110007820472',
                'out_order_no' => 'P20150806125346',
                'receivers' => '[{"type":"MERCHANT_ID","account":"190001001","amount":100,"description":"分到商户"},{"type":"PERSONAL_WECHATID","account":"86693952","amount":888,"description":"分到个人"}]',
            ]
        )->andReturn('mock-result');

        $this->assertSame('mock-result', $client->multiShare(
            '4208450740201411110007820472',
            'P20150806125346',
            [[
                'type' => 'MERCHANT_ID',
                'account' => '190001001',
                'amount' => 100,
                'description' => '分到商户',
            ], [
                'type' => 'PERSONAL_WECHATID',
                'account' => '86693952',
                'amount' => 888,
                'description' => '分到个人',
            ]]
        ));
    }

    public function testMarkOrderAsFinished()
    {
        $client = $this->mockApiClient(
            Client::class,
            ['safeRequest'],
            $this->app()
        );

        $client->expects()->safeRequest(
            'secapi/pay/profitsharingfinish',
            [
                'appid' => 'wx123456',
                'sub_appid' => null,
                'transaction_id' => '4208450740201411110007820472',
                'out_order_no' => 'P20150806125346',
                'amount' => 888,
                'description' => '分账已完成',
            ]
        )->andReturn('mock-result');

        $this->assertSame('mock-result', $client->markOrderAsFinished([
            'transaction_id' => '4208450740201411110007820472',
            'out_order_no' => 'P20150806125346',
            'amount' => 888,
            'description' => '分账已完成',
        ]));
    }

    public function testQuery()
    {
        $client = $this->mockApiClient(
            Client::class,
            ['request'],
            $this->app()
        );

        $client->expects()->request('pay/profitsharingquery', [
            'sub_appid' => null,
            'transaction_id' => '4208450740201411110007820472',
            'out_order_no' => 'P20150806125346',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->query(
            '4208450740201411110007820472',
            'P20150806125346'
        ));
    }

    public function testReturnShare()
    {
        $client = $this->mockApiClient(
            Client::class,
            ['safeRequest'],
            $this->app()
        );

        $outOrderNo = 'P20150806125346';
        $refundNo = 'bar';
        $amount = 888;
        $account = 'foo-merchant-id';
        $description = '用户退款';

        $client->expects()->safeRequest(
            'secapi/pay/profitsharingreturn',
            [
                'appid' => 'wx123456',
                'out_order_no' => $outOrderNo,
                'out_return_no' => $refundNo,
                'return_account_type' => 'MERCHANT_ID',
                'return_account' => $account,
                'return_amount' => $amount,
                'description' => $description,
            ]
        )->andReturn('mock-result');

        $this->assertSame('mock-result', $client->returnShare(
            $outOrderNo,
            $refundNo,
            $amount,
            $account,
            $description
        ));
    }
}
