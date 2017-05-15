<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Api;

class PreAuthorizationTest extends ApiTest
{
    public function testGetClientId()
    {
        $this->assertEquals('appid@foobar', $this->mockPreAuthorization('appid@foobar')->getClientId());
    }

    public function testGetCode()
    {
        $preAuth = $this->mockPreAuthorization('appid@foobar', 'code@foobar');

        $this->assertEquals('code@foobar', $preAuth->getCode());
    }

    public function testRedirect()
    {
        $response = $this->mockPreAuthorization('appid@foobar', 'code@foobar')->redirect('http://domain.com/callback.php');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=appid@foobar&pre_auth_code=code@foobar&redirect_uri=http%3A%2F%2Fdomain.com%2Fcallback.php', $response->getTargetUrl());
    }
}
