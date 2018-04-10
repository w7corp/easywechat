<?php
/**
 * Created by PhpStorm.
 * User: keal
 * Date: 2018/4/3
 * Time: 下午3:47
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\OfficialAccount\Auth;

use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Auth\Client;

class ClientTest extends TestCase
{
    public function testGetPreAuthorizationUrl()
    {
        $app = new Application(['app_id' => 'component-app-id']);

        $client = \Mockery::mock(Client::class.'[request]', [new Application(['app_id' => 'app-id']), $app]);

        $this->assertSame(
            'https://mp.weixin.qq.com/cgi-bin/fastregisterauth?copy_wx_verify=0&component_appid=component-app-id&appid=app-id&redirect_uri=https%3A%2F%2Feasywechat.com%2Fcallback',
            $client->getFastRegistrationUrl('https://easywechat.com/callback', false)
        );
    }
}