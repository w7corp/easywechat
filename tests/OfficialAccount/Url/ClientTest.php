<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Url;

use EasyWeChat\Applications\OfficialAccount\Url\Client as Url;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * Test shorten().
     */
    public function testShorten()
    {
        $url = \Mockery::mock('EasyWeChat\Applications\OfficialAccount\Url\Client[parseJSON]', [\Mockery::mock('EasyWeChat\Applications\OfficialAccount\Core\AccessToken')]);
        $url->shouldReceive('parseJSON')->andReturnUsing(function () {
            return func_get_args();
        });

        $response = $url->shorten('http://easywechat.org');

        $this->assertStringStartsWith(Url::API_SHORTEN_URL, $response[1][0]);
        $this->assertSame('long2short', $response[1][1]['action']);
        $this->assertSame('http://easywechat.org', $response[1][1]['long_url']);
    }
}
