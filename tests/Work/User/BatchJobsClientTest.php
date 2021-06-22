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
use EasyWeChat\Work\User\BatchJobsClient;

class BatchJobsClientTest extends TestCase
{
    public function testBatchUpdateUsers()
    {
        $client = $this->mockApiClient(BatchJobsClient::class);

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

        $this->assertSame('mock-result', $client->batchUpdateUsers($params));
    }

    public function testBatchReplaceUsers()
    {
        $client = $this->mockApiClient(BatchJobsClient::class);

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

        $this->assertSame('mock-result', $client->batchReplaceUsers($params));
    }

    public function testBatchReplaceDepartments()
    {
        $client = $this->mockApiClient(BatchJobsClient::class);

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

        $this->assertSame('mock-result', $client->batchReplaceDepartments($params));
    }

    public function testGetJobStatus()
    {
        $client = $this->mockApiClient(BatchJobsClient::class);

        $params = [
            'jobid' => 'jobidxxxx'
        ];

        $client->expects()->httpGet('cgi-bin/batch/getresult', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getJobStatus('jobidxxxx'));
    }
}
