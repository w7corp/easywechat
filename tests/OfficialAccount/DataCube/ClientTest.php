<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\DataCube;

use EasyWeChat\OfficialAccount\DataCube\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testUserSummary()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getusersummary', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userSummary('2017-08-02', '2017-08-10'));
    }

    public function testUserCumulate()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getusercumulate', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userCumulate('2017-08-02', '2017-08-10'));
    }

    public function testArticleSummary()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getarticlesummary', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->articleSummary('2017-08-02', '2017-08-10'));
    }

    public function testArticleTotal()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getarticletotal', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->articleTotal('2017-08-02', '2017-08-10'));
    }

    public function testUserReadSummary()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getuserread', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userReadSummary('2017-08-02', '2017-08-10'));
    }

    public function testUserReadHourly()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getuserreadhour', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userReadHourly('2017-08-02', '2017-08-10'));
    }

    public function testUserShareSummary()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getusershare', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userShareSummary('2017-08-02', '2017-08-10'));
    }

    public function testUserShareHourly()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getusersharehour', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userShareHourly('2017-08-02', '2017-08-10'));
    }

    public function testUpstreamMessageSummary()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getupstreammsg', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upstreamMessageSummary('2017-08-02', '2017-08-10'));
    }

    public function testUpstreamMessageHourly()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getupstreammsghour', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upstreamMessageHourly('2017-08-02', '2017-08-10'));
    }

    public function testUpstreamMessageWeekly()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getupstreammsgweek', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upstreamMessageWeekly('2017-08-02', '2017-08-10'));
    }

    public function testUpstreamMessageMonthly()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getupstreammsgmonth', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upstreamMessageMonthly('2017-08-02', '2017-08-10'));
    }

    public function testUpstreamMessageDistSummary()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getupstreammsgdist', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upstreamMessageDistSummary('2017-08-02', '2017-08-10'));
    }

    public function testUpstreamMessageDistWeekly()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getupstreammsgdistweek', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upstreamMessageDistWeekly('2017-08-02', '2017-08-10'));
    }

    public function testUpstreamMessageDistMonthly()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getupstreammsgdistmonth', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upstreamMessageDistMonthly('2017-08-02', '2017-08-10'));
    }

    public function testInterfaceSummary()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getinterfacesummary', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->interfaceSummary('2017-08-02', '2017-08-10'));
    }

    public function testInterfaceSummaryHourly()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getinterfacesummaryhour', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->interfaceSummaryHourly('2017-08-02', '2017-08-10'));
    }

    public function testCardSummary()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getcardbizuininfo', '2017-08-02', '2017-08-10', [
            'cond_source' => 67,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->cardSummary('2017-08-02', '2017-08-10', '67'));
    }

    public function testFreeCardSummary()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getcardcardinfo', '2017-08-02', '2017-08-10', [
            'cond_source' => 67,
            'card_id' => 'mock-card_id',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->freeCardSummary('2017-08-02', '2017-08-10', '67', 'mock-card_id'));
    }

    public function testMemberCardSummary()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getcardmembercardinfo', '2017-08-02', '2017-08-10', [
            'cond_source' => 67,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->memberCardSummary('2017-08-02', '2017-08-10', '67'));
    }

    public function testMemberCardSummaryById()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getcardmembercarddetail', '2017-08-02', '2017-08-10', [
            'card_id' => 'mock-card_id',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->memberCardSummaryById('2017-08-02', '2017-08-10', 'mock-card_id'));
    }

    public function testQuery()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $client->expects()->httpPostJson('path/to/api', [
            'begin_date' => '2017-08-02',
            'end_date' => '2017-08-10',
            'foo' => 'bar',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->query('path/to/api', '2017-08-02', '2017-08-10', ['foo' => 'bar']));
    }
}
