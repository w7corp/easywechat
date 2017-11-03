<?php
/**
 * Created by PhpStorm.
 * User: keal
 * Date: 2017/11/3
 * Time: 下午3:56
 */

namespace EasyWeChat\Tests\OpenPlatform\CodeTemplate;


use EasyWeChat\Tests\TestCase;
use EasyWeChat\OpenPlatform\CodeTemplate\Client;

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