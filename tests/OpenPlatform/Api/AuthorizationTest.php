<?php

/**
 * Test AuthorizerTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform\Api;

use EasyWeChat\OpenPlatform\AccessToken;
use EasyWeChat\OpenPlatform\Api\Authorization;
use EasyWeChat\Tests\TestCase;
use Mockery as m;

class AuthorizationTest extends TestCase
{
    /**
     * Authorization mock.
     *
     * @param string $appId
     *
     * @return \Mockery\MockInterface|\EasyWeChat\OpenPlatform\Api\Authorization
     */
    public function mockAuthorization($appId)
    {
        $authorization = m::mock(
            Authorization::class.'[parseJSON]',
            [
                m::mock(AccessToken::class),
                ['open_platform' => ['app_id' => $appId]],
            ]
        );

        /* @noinspection PhpUnusedParameterInspection */
        $authorization
            ->shouldReceive('parseJSON')
            ->andReturnUsing(function ($method, $params) {
                return [
                    'api' => $params[0],
                    'params' => empty($params[1]) ? null : $params[1],
                ];
            });

        return $authorization;
    }

    public function testGetAuthorizationInfo()
    {
        $authorizer = $this->mockAuthorization('appid@123');
        $result = $authorizer->getAuthorizationInfo('code@123');
        $expected = [
            'component_appid' => 'appid@123',
            'authorization_code' => 'code@123',
        ];

        $this->assertStringStartsWith(Authorization::GET_AUTH_INFO, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    public function testGetAuthorizationToken()
    {
        $authorizer = $this->mockAuthorization('appid@123');
        $result = $authorizer->getAuthorizationToken('appid@456', 'refresh@123');
        $expected = [
            'component_appid' => 'appid@123',
            'authorizer_appid' => 'appid@456',
            'authorizer_refresh_token' => 'refresh@123',
        ];

        $this->assertStringStartsWith(Authorization::GET_AUTHORIZER_TOKEN, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    public function testGetAuthorizerInfo()
    {
        $authorizer = $this->mockAuthorization('appid@123');
        $result = $authorizer->getAuthorizerInfo('appid@456');
        $expected = [
            'component_appid' => 'appid@123',
            'authorizer_appid' => 'appid@456',
        ];

        $this->assertStringStartsWith(Authorization::GET_AUTHORIZER_INFO, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    public function testGetAuthorizerOption()
    {
        $authorizer = $this->mockAuthorization('appid@123');
        $result = $authorizer->getAuthorizerOption('appid@456', 'option@123');
        $expected = [
            'component_appid' => 'appid@123',
            'authorizer_appid' => 'appid@456',
            'option_name' => 'option@123',
        ];

        $this->assertStringStartsWith(Authorization::GET_AUTHORIZER_OPTION, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    public function testSetAuthorizerOption()
    {
        $authorizer = $this->mockAuthorization('appid@123');
        $result = $authorizer->setAuthorizerOption('appid@456', 'option@123', 'value@123');
        $expected = [
            'component_appid' => 'appid@123',
            'authorizer_appid' => 'appid@456',
            'option_name' => 'option@123',
            'option_value' => 'value@123',
        ];

        $this->assertStringStartsWith(Authorization::SET_AUTHORIZER_OPTION, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }
}
