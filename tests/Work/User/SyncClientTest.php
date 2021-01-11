<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\User;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\User\SyncClient;

class SyncClientTest extends TestCase
{
    public function testBatchUpdateUser()
    {
        $client = $this->mockApiClient(SyncClient::class);

        $params = [
            'media_id' => 'xxxxxx',
            'to_invite' => true,
            'callback' => [
                'url' => 'xxx',
                'token' => 'xxx',
                'encodingaeskey' => 'xxx'
            ]
        ];

        $client->expects()->httpPostJson('cgi-bin/batch/syncuser', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->batchUpdateUser($params));
    }

    public function testBatchReplaceUser()
    {
        $client = $this->mockApiClient(SyncClient::class);

        $params = [
            'media_id' => 'xxxxxx',
            'to_invite' => true,
            'callback' => [
                'url' => 'xxx',
                'token' => 'xxx',
                'encodingaeskey' => 'xxx'
            ]
        ];

        $client->expects()->httpPostJson('cgi-bin/batch/replaceuser', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->batchReplaceUser($params));
    }

    public function testBatchReplaceParty()
    {
        $client = $this->mockApiClient(SyncClient::class);

        $params = [
            'media_id' => 'xxxxxx',
            'to_invite' => true,
            'callback' => [
                'url' => 'xxx',
                'token' => 'xxx',
                'encodingaeskey' => 'xxx'
            ]
        ];

        $client->expects()->httpPostJson('cgi-bin/batch/replaceparty', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->batchReplaceParty($params));
    }

    public function testGetResult()
    {
        $client = $this->mockApiClient(SyncClient::class);

        $params = [
            'jobid' => 'jobidxxxx'
        ];

        $client->expects()->httpGet('cgi-bin/batch/getresult', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getResult('jobidxxxx'));
    }
}