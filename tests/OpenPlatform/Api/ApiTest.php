<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Api;

use Doctrine\Common\Cache\ArrayCache;
use EasyWeChat\OpenPlatform\Api\AbstractOpenPlatform;
use EasyWeChat\OpenPlatform\Core\AccessToken;
use EasyWeChat\Tests\TestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;

class ApiTest extends TestCase
{
    protected function getAccessToken($appId)
    {
        $accessToken = new AccessToken(
            $appId,
            'secret'
        );
        $accessToken->setCache(new ArrayCache());

        return $accessToken->setVerifyTicket(m::mock('EasyWeChat\OpenPlatform\Core\VerifyTicket'));
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

    public function testGetClientId()
    {
        $api = new OpenPlatformApi($this->getAccessToken('app_id'), $this->getRequest());

        $this->assertEquals('app_id', $api->getClientId());
    }
}

class OpenPlatformApi extends AbstractOpenPlatform
{
}
