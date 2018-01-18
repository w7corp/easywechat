<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\OpenPlatform\EventHandlers\Authorized;
use EasyWeChat\OpenPlatform\EventHandlers\Unauthorized;
use EasyWeChat\Support\Collection;

class AuthorizationHandlerTest extends AuthorizationTest
{
    public function testAuthorized()
    {
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorizerAccessToken = 'access@123';
        $authorizerRefreshToken = 'refresh@123';
        $authorization = $this->make(
            $appId, $authorizerAppId,
            $authorizerAccessToken, $authorizerRefreshToken
        );

        $message = [
            'AppId' => 'open-platform-app-id',
            'CreateTIme' => '1413192760',
            'InfoType' => 'authorized',
            'AuthorizerAppid' => 'authorizer-app-id',
            'AuthorizationCode' => 'auth-code',
            'AuthorizationCodeExpiredTime' => '600',
        ];
        $authorized = new Authorized($authorization);
        $authorized->handle(new Collection($message));

        $this->assertEquals(
            $authorizerAccessToken,
            $authorization->getAuthorizerAccessToken()
        );
        $this->assertEquals(
            $authorizerRefreshToken,
            $authorization->getAuthorizerRefreshToken()
        );
    }

    public function testUnauthorized()
    {
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorizerAccessToken = 'access@123';
        $authorizerRefreshToken = 'refresh@123';
        $authorization = $this->make(
            $appId, $authorizerAppId,
            $authorizerAccessToken, $authorizerRefreshToken
        );

        // Authorized => saves the tokens.
        $message = [
            'AppId' => 'open-platform-app-id',
            'CreateTIme' => '1413192760',
            'InfoType' => 'authorized',
            'AuthorizerAppid' => 'authorizer-app-id',
            'AuthorizationCode' => 'auth-code',
            'AuthorizationCodeExpiredTime' => '600',
        ];
        $authorized = new Authorized($authorization);
        $authorized->handle(new Collection($message));

        // Unauthorized => removes the tokens.
        $message = [
            'AppId' => 'open-platform-app-id',
            'CreateTIme' => '1413192760',
            'InfoType' => 'authorized',
            'AuthorizerAppid' => 'authorizer-app-id',
        ];
        $authorized = new Unauthorized($authorization);
        $authorized->handle(new Collection($message));

        $this->assertNull($authorization->getAuthorizerAccessToken());
        $this->setExpectedException(\EasyWeChat\Core\Exception::class);
        $this->assertNull($authorization->getAuthorizerRefreshToken());
    }
}
