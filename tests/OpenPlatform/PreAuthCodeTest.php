<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\OpenPlatform\AccessToken;
use EasyWeChat\OpenPlatform\Components\PreAuthCode;

class PreAuthCodeTest extends TestCase
{
    /**
     * PreAuthCode mock.
     *
     * @param string $appId
     * @param string $code
     *
     * @return \Mockery\MockInterface|PreAuthCode
     */
    public function mockPreAuth($appId, $code = null)
    {
        $preAuth = Mockery::mock(
            PreAuthCode::class.'[parseJSON]',
            [
                Mockery::mock(AccessToken::class),
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
        $appId = 'appid@foobar';
        $this->assertEquals($appId, $this->mockPreAuth($appId)->getAppId());
    }

    public function testGetCode()
    {
        $appId = 'appid@foobar';
        $code = 'code@foobar';
        $preAuth = $this->mockPreAuth($appId, $code);

        /** @var PreAuthCode $preAuth */
        $result = $preAuth->getCode();

        $this->assertEquals($code, $result);
    }

    public function testGetAuthLink()
    {
        $appId = 'appid@foobar';
        $code = 'code@foobar';
        $redirect = 'http://domain.com/foo/bar';
        $preAuth = $this->mockPreAuth($appId, $code)
                        ->setRedirectUri($redirect);

        $link = sprintf(
            PreAuthCode::PRE_AUTH_LINK,
            $appId, $code, 'http%3a%2f%2fdomain.com%2ffoo%2fbar'
        );

        $this->assertEquals($link, strtolower($preAuth->getAuthLink()));
    }
}
