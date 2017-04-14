<?php

/**
 * Test AuthorizerTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform;

use Doctrine\Common\Cache\ArrayCache;
use EasyWeChat\OpenPlatform\Authorizer;
use EasyWeChat\Support\Collection;
use EasyWeChat\Tests\TestCase;

class AuthorizerTest extends TestCase
{
    public function testGetApi()
    {
        $authorizer = $this->make('appid', 'authorizer-appid');

        $this->assertInstanceOf('EasyWeChat\OpenPlatform\Api\BaseApi', $authorizer->getApi());
    }

    public function testGetAuthorizationInfo()
    {
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorizer = $this->make($appId, $authorizerAppId);

        $result = $authorizer->getApi()->getAuthorizationInfo();
        $this->assertEquals($this->stubAuthorizationInfo($authorizerAppId), $result);
    }

    public function testGetAuthorizerInfo()
    {
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorizer = $this->make($appId, $authorizerAppId);

        $result = $authorizer->getApi()->getAuthorizerInfo('appid@123');
        $this->assertEquals($this->stubAuthorizerInfo($authorizerAppId), $result);
    }

    public function testSetAndGetAccessToken()
    {
        $authorizer = $this->make('appid@123', 'authorizer-appid@456');
        $stub = $this->stubAuthorizationInfo('authorizer-appid@456', 'authorizer-access@123');

        $this->assertInstanceOf(
            'EasyWeChat\OpenPlatform\Authorizer',
            $authorizer->setAccessToken($stub['authorization_info']['authorizer_access_token'])
        );
        $this->assertEquals('authorizer-access@123', $authorizer->getAccessToken());
    }

    public function testSetAndGetRefreshToken()
    {
        $authorizer = $this->make('appid@123', 'appid@456');
        $stub = $this->stubAuthorizationInfo('appid@456', 'access@123', 'refresh@123');

        $this->assertInstanceOf(
            'EasyWeChat\OpenPlatform\Authorizer',
            $authorizer->setRefreshToken($stub['authorization_info']['authorizer_refresh_token'])
        );
        $this->assertEquals('refresh@123', $authorizer->getRefreshToken());
    }

    /**
     * Authorizer mock.
     *
     * @param string $appId
     * @param string $authorizerAppId
     * @param string $authorizerAccessToken
     * @param string $authorizerRefreshToken
     *
     * @return Authorizer
     */
    protected function make($appId, $authorizerAppId,
                          $authorizerAccessToken = null,
                          $authorizerRefreshToken = null)
    {
        /** @var Authorizer|\Mockery\MockInterface $mockAuthorizer */
        $mockAuthorizer = \Mockery::mock('EasyWeChat\OpenPlatform\Api\BaseApi');

        $mockAuthorizer
            ->shouldReceive('getAuthorizationInfo')
            ->andReturn(
                $this->stubAuthorizationInfo(
                    $authorizerAppId,
                    $authorizerAccessToken,
                    $authorizerRefreshToken
                )
            );

        $mockAuthorizer
            ->shouldReceive('getAuthorizerInfo')
            ->andReturn($this->stubAuthorizerInfo($authorizerAppId));

        $stub = $this->stubAuthorizerToken(
            $authorizerAccessToken, $authorizerRefreshToken
        );
        /* @noinspection PhpUnusedParameterInspection */
        $mockAuthorizer
            ->shouldReceive('getAuthorizerToken')
            ->andReturnUsing(
                function ($appId, $authorizerRefreshToken) use ($stub) {
                    return $stub;
                }
            );

        $cache = new ArrayCache();
        $authorizer = new Authorizer($mockAuthorizer, $appId, $cache);
        $authorizer->setAppId($authorizerAppId);

        return $authorizer;
    }

    protected function stubAuthorizationInfo($authorizerAppId,
                                           $authorizerAccessToken = null,
                                           $authorizerRefreshToken = null)
    {
        $overrides = [
            'authorization_info' => [
                'authorizer_appid' => $authorizerAppId,
            ],
        ];
        if ($authorizerAccessToken) {
            $overrides['authorization_info']['authorizer_access_token']
                = $authorizerAccessToken;
        }
        if ($authorizerRefreshToken) {
            $overrides['authorization_info']['authorizer_refresh_token']
                = $authorizerRefreshToken;
        }

        return $this->stub('authorization_info', $overrides);
    }

    protected function stubAuthorizerInfo($authorizerAppId)
    {
        $overrides = [
            'authorization_info' => [
                'appid' => $authorizerAppId,
            ],
        ];

        return $this->stub('authorizer_info', $overrides);
    }

    protected function stubAuthorizationAll($authorizerAppId,
                                          $authorizerAccessToken = null,
                                          $authorizerRefreshToken = null)
    {
        $overrides = [
            'authorization_info' => [
                'authorizer_appid' => $authorizerAppId,
                'authorizer_access_token' => $authorizerAccessToken,
                'authorizer_refresh_token' => $authorizerRefreshToken,
            ],
        ];

        return $this->stub('authorization_all', $overrides);
    }

    protected function stubAuthorizerToken($authorizerAccessToken,
                                         $authorizerRefreshToken)
    {
        $overrides = [
            'authorizer_access_token' => $authorizerAccessToken,
            'authorizer_refresh_token' => $authorizerRefreshToken,
        ];

        return $this->stub('authorizer_token', $overrides);
    }

    protected function stub($file, $overrides = null)
    {
        $json = file_get_contents("tests/OpenPlatform/stubs/{$file}.json");
        $data = json_decode($json, true);

        if ($overrides) {
            $data = $this->overrides($data, $overrides);
        }

        return new Collection($data);
    }

    protected function overrides(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->overrides($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
