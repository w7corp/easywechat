<?php

/**
 * Test PreAuthCodeTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform\Api;

use EasyWeChat\OpenPlatform\AccessToken;
use EasyWeChat\OpenPlatform\Api\PreAuthorization;
use Mockery as m;

class PreAuthorizationTest extends ApiTest
{
    public function testGetAppId()
    {
        $this->assertEquals('appid@foobar', $this->mockPreAuthorization('appid@foobar')->getAppId());
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
