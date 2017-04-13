<?php

/**
 * Test ApiTest.php.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform\Api;

use Doctrine\Common\Cache\ArrayCache;
use EasyWeChat\OpenPlatform\AccessToken;
use EasyWeChat\OpenPlatform\Api\AbstractOpenPlatform;
use EasyWeChat\Tests\TestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;

class ApiTest extends TestCase
{
    protected function getAccessToken($appId)
    {
        $accessToken = new AccessToken(
            $appId,
            'secret',
            new ArrayCache()
        );
        return $accessToken->setVerifyTicket(m::mock('EasyWeChat\OpenPlatform\VerifyTicket'));
    }

    protected function getRequest()
    {
        return new Request();
    }

    public function mockBaseApi($appId)
    {
        $baseApi = m::mock('EasyWeChat\OpenPlatform\Api\BaseApi[parseJSON]', [$this->getAccessToken($appId), $this->getRequest()]);
        /* @noinspection PhpUnusedParameterInspection */
        $baseApi->shouldReceive('parseJSON')
                ->andReturnUsing(function ($method, $params) {
                    return [
                        'api' => $params[0],
                        'params' => empty($params[1]) ? null : $params[1],
                    ];
            });

        return $baseApi;
    }

    protected function mockPreAuthorization($appId, $code = null)
    {
        $preAuth = m::mock('EasyWeChat\OpenPlatform\Api\PreAuthorization[parseJSON]',
            [$this->getAccessToken($appId), $this->getRequest()]
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
        $api = new OpenPlatformApi($this->getAccessToken('app_id'), $this->getRequest());

        $this->assertEquals('app_id', $api->getAppId());
    }
}

class OpenPlatformApi extends AbstractOpenPlatform
{
}
