<?php


namespace EasyWeChat\Tests\OfficialAccount\POI;


use EasyWeChat\OfficialAccount\POI\Client;
use EasyWeChat\Tests\TestCase;


class ClientTest extends TestCase
{
    public function testCategories()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/poi/getwxcategory')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->categories());
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/poi/getpoi', ['poi_id' => 44])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->get(44));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/poi/delpoi', ['poi_id' => 12])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->delete(12));
    }

    public function testLists()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/poi/getpoilist', [
            'begin' => 0,
            'limit' => 10
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->lists());

        $client->expects()->httpPostJson('cgi-bin/poi/getpoilist', [
            'begin' => 1,
            'limit' => 20
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->lists(1, 20));
    }

    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/poi/addpoi', [
            'business' => [
                'base_info' => ['foo' => 'bar'],
            ]
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->create(['foo' => 'bar']));
    }

    public function testCreateAndGetId()
    {
        $client = $this->mockApiClient(Client::class, ['create']);

        $client->expects()->create(['foo' => 'bar'])->andReturn(['poi_id' => 'mock-id'])->once();
        $this->assertSame('mock-id', $client->createAndGetId(['foo' => 'bar']));
    }


    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/poi/updatepoi', [
            'business' => [
                'base_info' => [
                    'foo' => 'bar',
                    'poi_id' => 246
                ],
            ]
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->update(246, ['foo' => 'bar']));
    }
}