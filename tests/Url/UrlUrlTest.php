<?php

use EasyWeChat\Url\Url;

class UrlUrlTest extends TestCase
{
    public function testShorten()
    {
        $http = Mockery::mock(EasyWeChat\Core\Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return ['short_url' => $params['long_url']];
        });

        $url = new Url($http);

        $this->assertEquals('http://easywechat.org', $url->shorten('http://easywechat.org'));
    }
}
