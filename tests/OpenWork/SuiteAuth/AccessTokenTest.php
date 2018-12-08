<?php

namespace EasyWeChat\Tests\OpenWork\SuiteAuth;


use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenWork\SuiteAuth\AccessToken;
use EasyWeChat\OpenWork\SuiteAuth\SuiteTicket;
use EasyWeChat\Tests\TestCase;

class AccessTokenTest extends TestCase
{
    public function testGetCredentials()
    {
        $suitTicket = \Mockery::mock(SuiteTicket::class, function ($mock) {
            $mock->expects()->getTicket()->andReturn('mock-suit-ticket')->once();
        });

        $app = new ServiceContainer([
            'suite_id' => 'mock-suite-id',
            'suite_secret' => 'mock-suite-secret',
        ], ['suite_ticket' => $suitTicket]);

        $accessToken = \Mockery::mock(AccessToken::class, [$app])->makePartial()->shouldAllowMockingProtectedMethods();

        $this->assertSame([
            'suite_id' => 'mock-suite-id',
            'suite_secret' => 'mock-suite-secret',
            'suite_ticket' => 'mock-suit-ticket',
        ], $accessToken->getCredentials());
    }
}