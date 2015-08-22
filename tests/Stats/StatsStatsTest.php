<?php

use EasyWeChat\Stats\Stats;

use EasyWeChat\Core\Http;

class StatsStatsTest extends TestCase
{
    /**
     * Http.
     *
     * @return \Mockery\MockInterface
     */
    public function getHttp()
    {
        $http = Mockery::mock(Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function($api, $params){
            return ['list' => compact('api', 'params')];
        });

        return $http;
    }


    /**
     * Test userSummary()
     */
    public function testuserSummary()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->userSummary(1, 2);
        $this->assertEquals(Stats::API_USER_SUMMARY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test userCumulate()
     */
    public function testuserCumulate()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->userCumulate(1, 2);
        $this->assertEquals(Stats::API_USER_CUMULATE, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test articleSummary()
     */
    public function testarticleSummary()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->articleSummary(1, 2);
        $this->assertEquals(Stats::API_ARTICLE_SUMMARY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test articleTotal()
     */
    public function testarticleTotal()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->articleTotal(1, 2);
        $this->assertEquals(Stats::API_ARTICLE_TOTAL, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test userReadSummary()
     */
    public function testuserReadSummary()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->userReadSummary(1, 2);
        $this->assertEquals(Stats::API_USER_READ_SUMMARY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test userReadHourly()
     */
    public function testuserReadHourly()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->userReadHourly(1, 2);
        $this->assertEquals(Stats::API_USER_READ_HOURLY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test userShareSummary()
     */
    public function testuserShareSummary()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->userShareSummary(1, 2);
        $this->assertEquals(Stats::API_USER_SHARE_SUMMARY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test userShareHourly()
     */
    public function testuserShareHourly()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->userShareHourly(1, 2);
        $this->assertEquals(Stats::API_USER_SHARE_HOURLY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test upstreamMessageSummary()
     */
    public function testupstreamMessageSummary()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->upstreamMessageSummary(1, 2);
        $this->assertEquals(Stats::API_UPSTREAM_MSG_SUMMARY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test upstreamMessageHourly()
     */
    public function testupstreamMessageHourly()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->upstreamMessageHourly(1, 2);
        $this->assertEquals(Stats::API_UPSTREAM_MSG_HOURLY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test upstreamMessageWeekly()
     */
    public function testupstreamMessageWeekly()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->upstreamMessageWeekly(1, 2);
        $this->assertEquals(Stats::API_UPSTREAM_MSG_WEEKLY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test upstreamMessageMonthly()
     */
    public function testupstreamMessageMonthly()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->upstreamMessageMonthly(1, 2);
        $this->assertEquals(Stats::API_UPSTREAM_MSG_MONTHLY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test upstreamMessageDistSummary()
     */
    public function testupstreamMessageDistSummary()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->upstreamMessageDistSummary(1, 2);
        $this->assertEquals(Stats::API_UPSTREAM_MSG_DIST_SUMMARY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test upstreamMessageDistWeekly()
     */
    public function testupstreamMessageDistWeekly()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->upstreamMessageDistWeekly(1, 2);
        $this->assertEquals(Stats::API_UPSTREAM_MSG_DIST_WEEKLY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test upstreamMessageDistMonthly()
     */
    public function testupstreamMessageDistMonthly()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->upstreamMessageDistMonthly(1, 2);
        $this->assertEquals(Stats::API_UPSTREAM_MSG_DIST_MONTHLY, $result['api']);
        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test interfaceSummary()
     */
    public function testinterfaceSummary()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->interfaceSummary(1, 2);
        $this->assertEquals(Stats::API_INTERFACE_SUMMARY, $result['api']);

        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }

    /**
     * Test interfaceSummaryHourly()
     */
    public function testinterfaceSummaryHourly()
    {
        $stats = new Stats($this->getHttp());

        $result = $stats->interfaceSummaryHourly(1, 2);
        $this->assertEquals(Stats::API_INTERFACE_SUMMARY_HOURLY, $result['api']);

        $this->assertEquals(['begin_date' => 1, 'end_date' => 2], $result['params']);
    }
}