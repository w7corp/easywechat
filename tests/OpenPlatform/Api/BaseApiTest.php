<?php

/**
 * Test BaseApiTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform\Api;

use EasyWeChat\OpenPlatform\Api\BaseApi;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;

class BaseApiTest extends ApiTest
{
    public function testGetAuthorizationInfo()
    {
        $authorizer = $this->mockBaseApi('appid@123');
        $result = $authorizer->getAuthorizationInfo('code@123');
        $expected = [
            'component_appid' => 'appid@123',
            'authorization_code' => 'code@123',
        ];

        $this->assertStringStartsWith(BaseApi::GET_AUTH_INFO, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    public function testGetAuthorizerToken()
    {
        $authorizer = $this->mockBaseApi('appid@123');
        $result = $authorizer->getAuthorizerToken('appid@456', 'refresh@123');
        $expected = [
            'component_appid' => 'appid@123',
            'authorizer_appid' => 'appid@456',
            'authorizer_refresh_token' => 'refresh@123',
        ];

        $this->assertStringStartsWith(BaseApi::GET_AUTHORIZER_TOKEN, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    public function testGetAuthorizerInfo()
    {
        $authorizer = $this->mockBaseApi('appid@123');
        $result = $authorizer->getAuthorizerInfo('appid@456');
        $expected = [
            'component_appid' => 'appid@123',
            'authorizer_appid' => 'appid@456',
        ];

        $this->assertStringStartsWith(BaseApi::GET_AUTHORIZER_INFO, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    public function testGetAuthorizerOption()
    {
        $authorizer = $this->mockBaseApi('appid@123');
        $result = $authorizer->getAuthorizerOption('appid@456', 'option@123');
        $expected = [
            'component_appid' => 'appid@123',
            'authorizer_appid' => 'appid@456',
            'option_name' => 'option@123',
        ];

        $this->assertStringStartsWith(BaseApi::GET_AUTHORIZER_OPTION, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    public function testSetAuthorizerOption()
    {
        $authorizer = $this->mockBaseApi('appid@123');
        $result = $authorizer->setAuthorizerOption('appid@456', 'option@123', 'value@123');
        $expected = [
            'component_appid' => 'appid@123',
            'authorizer_appid' => 'appid@456',
            'option_name' => 'option@123',
            'option_value' => 'value@123',
        ];

        $this->assertStringStartsWith(BaseApi::SET_AUTHORIZER_OPTION, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }
}
