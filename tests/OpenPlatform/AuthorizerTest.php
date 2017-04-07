<?php

/**
 * Test AuthorizerTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\OpenPlatform\AccessToken;
use EasyWeChat\OpenPlatform\Components\Authorizer;
use EasyWeChat\Tests\TestCase;

class AuthorizerTest extends TestCase
{
    /**
     * Authorizer mock.
     *
     * @param $appId
     *
     * @return \Mockery\MockInterface|Authorizer
     */
    public function mockAuthorizer($appId)
    {
        $authorizer = \Mockery::mock(
            Authorizer::class.'[parseJSON]',
            [
                \Mockery::mock(AccessToken::class),
                ['open_platform' => ['app_id' => $appId]],
            ]
        );

        /* @noinspection PhpUnusedParameterInspection */
        $authorizer
            ->shouldReceive('parseJSON')
            ->andReturnUsing(function ($method, $params) {
                return [
                    'api' => $params[0],
                    'params' => empty($params[1]) ? null : $params[1],
                ];
            });

        return $authorizer;
    }

    public function testGetAuthorizationInfo()
    {
        $appId = 'appid@123';
        $authorizer = $this->mockAuthorizer($appId);

        $code = 'code@123';
        $result = $authorizer->getAuthorizationInfo($code);

        $params = [
            'component_appid' => $appId,
            'authorization_code' => $code,
        ];
        $this->assertStringStartsWith(Authorizer::GET_AUTH_INFO, $result['api']);
        $this->assertEquals($params, $result['params']);
    }

    public function testGetAuthorizationToken()
    {
        $appId = 'appid@123';
        $authorizer = $this->mockAuthorizer($appId);

        $authorizerAppId = 'appid@456';
        $refreshToken = 'refresh@123';
        $result = $authorizer->getAuthorizationToken($authorizerAppId, $refreshToken);

        $params = [
            'component_appid' => $appId,
            'authorizer_appid' => $authorizerAppId,
            'authorizer_refresh_token' => $refreshToken,
        ];
        $this->assertStringStartsWith(Authorizer::GET_AUTHORIZER_TOKEN, $result['api']);
        $this->assertEquals($params, $result['params']);
    }

    public function testGetAuthorizerInfo()
    {
        $appId = 'appid@123';
        $authorizer = $this->mockAuthorizer($appId);

        $authorizerAppId = 'appid@456';
        $result = $authorizer->getAuthorizerInfo($authorizerAppId);

        $params = [
            'component_appid' => $appId,
            'authorizer_appid' => $authorizerAppId,
        ];
        $this->assertStringStartsWith(Authorizer::GET_AUTHORIZER_INFO, $result['api']);
        $this->assertEquals($params, $result['params']);
    }

    public function testGetAuthorizerOption()
    {
        $appId = 'appid@123';
        $authorizer = $this->mockAuthorizer($appId);

        $authorizerAppId = 'appid@456';
        $optionName = 'option@123';
        $result = $authorizer->getAuthorizerOption($authorizerAppId, $optionName);

        $params = [
            'component_appid' => $appId,
            'authorizer_appid' => $authorizerAppId,
            'option_name' => $optionName,
        ];
        $this->assertStringStartsWith(Authorizer::GET_AUTHORIZER_OPTION, $result['api']);
        $this->assertEquals($params, $result['params']);
    }

    public function testSetAuthorizerOption()
    {
        $appId = 'appid@123';
        $authorizer = $this->mockAuthorizer($appId);

        $authorizerAppId = 'appid@456';
        $optionName = 'option@123';
        $optionValue = 'value@123';
        $result = $authorizer->setAuthorizerOption(
            $authorizerAppId, $optionName, $optionValue);

        $params = [
            'component_appid' => $appId,
            'authorizer_appid' => $authorizerAppId,
            'option_name' => $optionName,
            'option_value' => $optionValue,
        ];
        $this->assertStringStartsWith(Authorizer::SET_AUTHORIZER_OPTION, $result['api']);
        $this->assertEquals($params, $result['params']);
    }
}
