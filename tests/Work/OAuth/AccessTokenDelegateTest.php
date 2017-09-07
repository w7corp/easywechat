<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\OAuth;

use EasyWeChat\Kernel\AccessToken;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Application;
use EasyWeChat\Work\OAuth\AccessTokenDelegate;

class AccessTokenDelegateTest extends TestCase
{
    public function testGetToken()
    {
        $accessToken = \Mockery::mock(AccessToken::class);
        $accessToken->allows()->getToken()->andReturn(['access_token' => 'mock-token']);

        $delegate = new AccessTokenDelegate(new Application([], [
            'access_token' => $accessToken,
        ]));

        $this->assertSame('mock-token', $delegate->getToken());
    }
}
