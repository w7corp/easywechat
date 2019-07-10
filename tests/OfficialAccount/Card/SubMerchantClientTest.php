<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Card;

use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\OfficialAccount\Card\SubMerchantClient;
use EasyWeChat\Tests\TestCase;

class SubMerchantClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(SubMerchantClient::class);

        $info = [
            'brand_name' => 'mock-bran_name',
            'logo_url' => 'mock-logo_url',
            'protocol' => 'mock-protocol',
            'end_time' => 'mock-end_time',
            'primary_category_id' => 'mock-primary_category_id',
            'secondary_category_id' => 'mock-secondary_category_id',
            'agreement_media_id' => 'mock-agreement_media_id',
            'operator_media_id' => 'mock-operator_media_id',
            'app_id' => 'mock-app_id',
            'foo' => 'bar',
        ];

        $client->expects()->httpPostJson('card/submerchant/submit', ['info' => Arr::except($info, 'foo')])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create($info));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(SubMerchantClient::class);

        $info = [
            'brand_name' => 'mock-bran_name',
            'logo_url' => 'mock-logo_url',
            'protocol' => 'mock-protocol',
            'end_time' => 'mock-end_time',
            'primary_category_id' => 'mock-primary_category_id',
            'secondary_category_id' => 'mock-secondary_category_id',
            'agreement_media_id' => 'mock-agreement_media_id',
            'operator_media_id' => 'mock-operator_media_id',
            'app_id' => 'mock-app_id',
            'foo' => 'bar',
        ];

        $client->expects()->httpPostJson('card/submerchant/update', [
            'info' => array_merge(['merchant_id' => 13], Arr::except($info, 'foo')),
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update(13, $info));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(SubMerchantClient::class);

        $client->expects()->httpPostJson('card/submerchant/get', ['merchant_id' => 13])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get(13));
    }

    public function testList()
    {
        $client = $this->mockApiClient(SubMerchantClient::class);

        $client->expects()->httpPostJson('card/submerchant/batchget', [
            'begin_id' => 0,
            'limit' => 50,
            'status' => 'CHECKING',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list());

        $client->expects()->httpPostJson('card/submerchant/batchget', [
            'begin_id' => 10,
            'limit' => 20,
            'status' => 'CHECKED',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list(10, 20, 'CHECKED'));
    }
}
