<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\CodeTemplate;

use EasyWeChat\OpenPlatform\CodeTemplate\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGetDrafts()
    {
        $client = $this->mockApiClient(Client::class, []);

        $client->expects()->httpGet('wxa/gettemplatedraftlist')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->getDrafts());
    }

    public function testAddDraftToTemplate()
    {
        $client = $this->mockApiClient(Client::class, []);

        $client->expects()->httpPostJson('wxa/addtotemplate', ['draft_id' => 123])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->addDraftToTemplate(123));
    }

    public function testGetTemplates()
    {
        $client = $this->mockApiClient(Client::class, []);

        $client->expects()->httpGet('wxa/gettemplatelist')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->getTemplates());
    }

    public function testDeleteTemplate()
    {
        $client = $this->mockApiClient(Client::class, []);

        $client->expects()->httpPostJson('wxa/deletetemplate', ['template_id' => 234])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->deleteTemplate(234));
    }
}
