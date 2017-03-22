<?php

/**
 * Test AuthorizationTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */
use Doctrine\Common\Cache\ArrayCache;
use EasyWeChat\OpenPlatform\Authorization;
use EasyWeChat\OpenPlatform\Components\Authorizer;

class AuthorizationTest extends TestCase
{
    public function testGetAuthorizationInfo()
    {
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorization = $this->make($appId, $authorizerAppId);

        $result = $authorization->getAuthorizationInfo();
        $this->assertEquals($this->stubAuthorizationInfo($authorizerAppId), $result->all());
    }

    public function testGetAuthorizerInfo()
    {
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorization = $this->make($appId, $authorizerAppId);

        $result = $authorization->getAuthorizerInfo();
        $this->assertEquals($this->stubAuthorizerInfo($authorizerAppId), $result->all());
    }

    public function testSaveAndGetAuthorizerAccessToken()
    {
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorizerAccessToken = 'access@123';
        $authorization = $this->make($appId, $authorizerAppId);

        $stub = $this->stubAuthorizationInfo(
            $authorizerAppId, $authorizerAccessToken);

        $authorization->saveAuthorizerAccessToken($stub['authorization_info']);
        $result = $authorization->getAuthorizerAccessToken();

        $this->assertEquals($authorizerAccessToken, $result);
    }

    public function testSaveAndGetAuthorizerRefreshToken()
    {
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorizerAccessToken = 'access@123';
        $authorizerRefreshToken = 'refresh@123';
        $authorization = $this->make($appId, $authorizerAppId);

        $stub = $this->stubAuthorizationInfo($authorizerAppId,
            $authorizerAccessToken, $authorizerRefreshToken);

        $authorization->saveAuthorizerRefreshToken($stub['authorization_info']);
        $result = $authorization->getAuthorizerRefreshToken();

        $this->assertEquals($authorizerRefreshToken, $result);
    }

    public function testHandleAuthorizerAccessToken()
    {
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorizerAccessToken = 'access@123';
        $authorizerRefreshToken = 'refresh@123';
        $authorization = $this->make(
            $appId, $authorizerAppId,
            $authorizerAccessToken, $authorizerRefreshToken
        );

        $stub = $this->stubAuthorizationInfo($authorizerAppId, $authorizerRefreshToken);
        $authorization->saveAuthorizerRefreshToken($stub['authorization_info']);

        $result = $authorization->handleAuthorizerAccessToken();
        $this->assertEquals($authorizerAccessToken, $result);
    }

    public function testHandleAuthorization()
    {
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorizerAccessToken = 'access@123';
        $authorizerRefreshToken = 'refresh@123';
        $authorization = $this->make(
            $appId, $authorizerAppId,
            $authorizerAccessToken, $authorizerRefreshToken
        );

        $stub = $this->stubAuthorizationAll($authorizerAppId,
            $authorizerAccessToken, $authorizerRefreshToken);
        $result = $authorization->handleAuthorization();
        $this->assertEquals($stub, $result->all());

        $savedAccessToken = $authorization->getAuthorizerAccessToken();
        $savedRefreshToken = $authorization->getAuthorizerRefreshToken();

        $this->assertEquals($authorizerAccessToken, $savedAccessToken);
        $this->assertEquals($authorizerRefreshToken, $savedRefreshToken);
    }

    /**
     * Authorization mock.
     *
     * @param string $appId
     * @param string $authorizerAppId
     * @param string $authorizerAccessToken
     * @param string $authorizerRefreshToken
     *
     * @return Authorization
     */
    protected function make($appId, $authorizerAppId,
                          $authorizerAccessToken = null,
                          $authorizerRefreshToken = null)
    {
        /** @var Authorizer|\Mockery\MockInterface $mockAuthorizer */
        $mockAuthorizer = Mockery::mock(Authorizer::class);

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
            ->shouldReceive('getAuthorizationToken')
            ->andReturnUsing(
                function ($appId, $authorizerRefreshToken) use ($stub) {
                    return $stub;
                }
            );

        $cache = new ArrayCache();
        $authorization = new Authorization($mockAuthorizer, $appId, $cache);
        $authorization->setAuthorizerAppId($authorizerAppId);

        return $authorization;
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

        return $data;
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
