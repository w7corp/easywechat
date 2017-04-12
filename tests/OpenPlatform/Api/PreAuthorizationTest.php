<?php

/**
 * Test PreAuthCodeTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform\Api;

use EasyWeChat\OpenPlatform\AccessToken;
use EasyWeChat\OpenPlatform\Api\PreAuthorization;
use EasyWeChat\Tests\TestCase;
use Mockery as m;

class PreAuthorizationTest extends TestCase
{
    /**
     * PreAuth mock.
     *
     * @param string $appId
     * @param string $code
     *
     * @return \Mockery\MockInterface|PreAuth
     */
    public function mockPreAuthorization($appId, $code = null)
    {
        $preAuth = m::mock(
            PreAuthorization::class.'[parseJSON]',
            [
                m::mock(AccessToken::class),
                ['open_platform' => ['app_id' => $appId]],
            ]
        );

        /* @noinspection PhpUnusedParameterInspection */
        $preAuth
            ->shouldReceive('parseJSON')
            ->andReturnUsing(function ($method, $params) use ($code) {
                return [
                    'api' => $params[0],
                    'params' => empty($params[1]) ? null : $params[1],
                    'pre_auth_code' => $code,
                ];
            });

        return $preAuth;
    }

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
