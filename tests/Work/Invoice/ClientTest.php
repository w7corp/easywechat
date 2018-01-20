<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Invoice;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Invoice\Client;

class ClientTest extends TestCase
{
    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/card/invoice/reimburse/getinvoiceinfo', [
            'card_id' => 'mock-id',
            'encrypt_code' => 'mock-encrypt_code',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('mock-id', 'mock-encrypt_code'));
    }

    public function testSelect()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/card/invoice/reimburse/getinvoiceinfobatch', [
            'item_list' => [
                ['card_id' => 'cardid1', 'encrypt_code' => 'code1'],
                ['card_id' => 'cardid2', 'encrypt_code' => 'code2'],
            ],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->select([
            ['card_id' => 'cardid1', 'encrypt_code' => 'code1'],
            ['card_id' => 'cardid2', 'encrypt_code' => 'code2'],
        ]));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/card/invoice/reimburse/updateinvoicestatus', [
            'card_id' => 'mock-id',
            'encrypt_code' => 'mock-encrypt_code',
            'reimburse_status' => 'INVOICE_REIMBURSE_INIT',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update('mock-id', 'mock-encrypt_code', 'INVOICE_REIMBURSE_INIT'));
    }

    public function testBatchUpdate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/card/invoice/reimburse/updatestatusbatch', [
            'openid' => 'mock-openid',
            'reimburse_status' => 'INVOICE_REIMBURSE_INIT',
            'invoice_list' => [
                ['card_id' => 'cardid1', 'encrypt_code' => 'code1'],
                ['card_id' => 'cardid2', 'encrypt_code' => 'code2'],
            ],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->batchUpdate([
            ['card_id' => 'cardid1', 'encrypt_code' => 'code1'],
            ['card_id' => 'cardid2', 'encrypt_code' => 'code2'],
        ], 'mock-openid', 'INVOICE_REIMBURSE_INIT'));
    }
}
