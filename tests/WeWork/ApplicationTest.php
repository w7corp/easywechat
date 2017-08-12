<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\WeWork;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\WeWork\Application;
use EasyWeChat\WeWork\Base\Client;

class ApplicationTest extends TestCase
{
    public function testInstances()
    {
        $app = new Application(['agent_id' => 102093]);

        $this->assertInstanceOf(\EasyWeChat\WeWork\OA\Client::class, $app->oa);
        $this->assertInstanceOf(\EasyWeChat\WeWork\Auth\AccessToken::class, $app->access_token);
        $this->assertInstanceOf(\EasyWeChat\WeWork\Agent\Client::class, $app->agent);
        $this->assertInstanceOf(\EasyWeChat\WeWork\Department\Client::class, $app->department);
        $this->assertInstanceOf(\EasyWeChat\WeWork\Media\Client::class, $app->media);
        $this->assertInstanceOf(\EasyWeChat\WeWork\Menu\Client::class, $app->menu);
        $this->assertInstanceOf(\EasyWeChat\WeWork\Message\Client::class, $app->message);
        $this->assertInstanceOf(\EasyWeChat\WeWork\Message\Messenger::class, $app->messenger);
        $this->assertInstanceOf(\EasyWeChat\WeWork\Server\Guard::class, $app->server);
        $this->assertInstanceOf(\EasyWeChat\BasicService\Jssdk\Client::class, $app->jssdk);
        $this->assertInstanceOf(\Overtrue\Socialite\Providers\WeWorkProvider::class, $app->oauth);
    }

    public function testBaseCall()
    {
        $client = \Mockery::mock(Client::class);
        $client->expects()->getCallbackIp(1, 2, 3)->andReturn('mock-result');

        $app = new Application([]);
        $app['base'] = $client;

        $this->assertSame('mock-result', $app->getCallbackIp(1, 2, 3));
    }
}
