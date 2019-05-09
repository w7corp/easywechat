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

use EasyWeChat\OfficialAccount\Card\CodeClient;
use EasyWeChat\Tests\TestCase;

class CodeClientTest extends TestCase
{
    public function testDeposit()
    {
        $client = $this->mockApiClient(CodeClient::class);

        $cardId = 'mock-card-id';
        $params = [
            'card_id' => $cardId,
            'code' => ['code1', 'code2'],
        ];
        $client->expects()->httpPostJson('card/code/deposit', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->deposit($cardId, ['code1', 'code2']));
    }

    public function testGetDepositedCount()
    {
        $client = $this->mockApiClient(CodeClient::class);

        $cardId = 'mock-card-id';
        $params = [
            'card_id' => $cardId,
        ];
        $client->expects()->httpPostJson('card/code/getdepositcount', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getDepositedCount($cardId));
    }

    public function testCheck()
    {
        $client = $this->mockApiClient(CodeClient::class);

        $cardId = 'mock-card-id';
        $code = 'mock-code';
        $params = [
            'card_id' => $cardId,
            'code' => ['code1', 'code2'],
        ];
        $client->expects()->httpPostJson('card/code/checkcode', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->check($cardId, ['code1', 'code2']));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(CodeClient::class);

        $cardId = 'mock-card-id';
        $code = 'mock-code';
        $client->expects()->httpPostJson('card/code/get', [
            'code' => $code,
            'check_consume' => true,
            'card_id' => $cardId,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get($code, $cardId));

        $client->expects()->httpPostJson('card/code/get', [
            'code' => $code,
            'check_consume' => false,
            'card_id' => $cardId,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get($code, $cardId, false));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(CodeClient::class);

        $cardId = 'mock-card-id';
        $newCode = 'mock-new-code';
        $code = 'mock-code';
        $params = [
            'code' => $code,
            'new_code' => $newCode,
            'card_id' => $cardId,
        ];
        $client->expects()->httpPostJson('card/code/update', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update($code, $newCode, $cardId));
    }

    public function testDisable()
    {
        $client = $this->mockApiClient(CodeClient::class);

        $cardId = 'mock-card-id';
        $code = 'mock-code';
        $params = [
            'code' => $code,
            'card_id' => $cardId,
        ];
        $client->expects()->httpPostJson('card/code/unavailable', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->disable($code, $cardId));
    }

    public function testConsume()
    {
        $client = $this->mockApiClient(CodeClient::class);

        $cardId = 'mock-card-id';
        $code = 'mock-code';

        $client->expects()->httpPostJson('card/code/consume', ['code' => $code])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->consume($code));

        $client->expects()->httpPostJson('card/code/consume', ['code' => $code, 'card_id' => $cardId])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->consume($code, $cardId));
    }

    public function testDecrypt()
    {
        $client = $this->mockApiClient(CodeClient::class);

        $encryptedCode = 'mock-encrypted-code';
        $params = [
            'encrypt_code' => $encryptedCode,
        ];
        $client->expects()->httpPostJson('card/code/decrypt', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->decrypt($encryptedCode));
    }
}
