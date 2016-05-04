<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Url\Url;

class UrlUrlTest extends TestCase
{
    /**
     * Test shorten().
     */
    public function testShorten()
    {
        $url = Mockery::mock('EasyWeChat\Url\Url[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $url->shouldReceive('parseJSON')->andReturnUsing(function () {
            return func_get_args();
        });

        $response = $url->shorten('http://easywechat.org');

        $this->assertStringStartsWith(Url::API_SHORTEN_URL, $response[1][0]);
        $this->assertEquals('long2short', $response[1][1]['action']);
        $this->assertEquals('http://easywechat.org', $response[1][1]['long_url']);
    }
}
