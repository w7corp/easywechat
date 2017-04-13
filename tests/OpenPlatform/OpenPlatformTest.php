<?php

/**
 * Test OpenPlatformTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform;

use Doctrine\Common\Cache\ArrayCache;
use EasyWeChat\Foundation\Application;
use EasyWeChat\OpenPlatform\AccessToken;
use EasyWeChat\OpenPlatform\VerifyTicket;
use EasyWeChat\Tests\TestCase;
use Mockery as m;

class OpenPlatformTest extends TestCase
{
    public function testOpenPlatform()
    {
        $openPlatform = $this->make()->open_platform;

        $this->assertInstanceOf('EasyWeChat\OpenPlatform\Api\BaseApi', $openPlatform->api);
        $this->assertInstanceOf('EasyWeChat\OpenPlatform\Api\PreAuthorization', $openPlatform->pre_auth);
        $this->assertInstanceOf('EasyWeChat\OpenPlatform\Api\PreAuthorization', $openPlatform->pre_authorization);
        $this->assertInstanceOf('EasyWeChat\OpenPlatform\Guard', $openPlatform->server);
    }

    public function testMakeAuthorizer()
    {
        $verifyTicket = new VerifyTicket('open-platform-appid@999', new ArrayCache());
        $verifyTicket->setTicket('ticket');

        $cache = m::mock('Doctrine\Common\Cache\Cache', function ($mock) {
            $mock->shouldReceive('fetch')->andReturn('thisIsACachedToken');
            $mock->shouldReceive('save')->andReturnUsing(function ($key, $token, $expire) {
                return $token;
            });
        });
        $accessToken = new AccessToken(
            'open-platform-appid@999',
            'open-platform-secret',
            $cache
        );
        $accessToken->setVerifyTicket($verifyTicket);

        $app = $this->make();
        $app['open_platform.access_token'] = $accessToken;
        $newApp = $app->open_platform->createAuthorizer('authorizer-appid@999', 'authorizer-refresh-token');

        $this->assertInstanceOf('EasyWeChat\OpenPlatform\AuthorizerAccessToken', $newApp->access_token);
        $this->assertEquals('authorizer-appid@999', $newApp->access_token->getAppId());
    }

    /**
     * Makes application.
     *
     * @return Application
     */
    private function make()
    {
        $config = [
            'app_id' => 'init-appid',
            'secret' => 'init-secret',
            'open_platform' => [
                'app_id' => 'your-app-id',
                'secret' => 'your-app-secret',
                'token' => 'your-token',
                'aes_key' => 'your-ase-key',
            ],
        ];

        return new Application($config);
    }
}
