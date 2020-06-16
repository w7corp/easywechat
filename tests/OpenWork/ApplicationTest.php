<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenWork;

use EasyWeChat\OpenWork\Application;
use EasyWeChat\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testProperties()
    {
        $app = new Application(['corp_id' => 'mock-corp-id']);

        $this->assertInstanceOf(\EasyWeChat\OpenWork\Server\Guard::class, $app->server);
        $this->assertInstanceOf(\EasyWeChat\OpenWork\Corp\Client::class, $app->corp);
        $this->assertInstanceOf(\EasyWeChat\OpenWork\Provider\Client::class, $app->provider);
        $this->assertInstanceOf(\EasyWeChat\OpenWork\MiniProgram\Client::class, $app->mini_program);
    }

    public function testWork()
    {
        $app = new Application(['corp_id' => 'mock-corp-id']);
        $work = $app->work('mock-auth-corp-id', 'mock-permanent-code');

        $this->assertInstanceOf('\EasyWeChat\OpenWork\Work\Application', $work);
        $this->assertInstanceOf('EasyWeChat\OpenWork\Work\Auth\AccessToken', $work->access_token);

        $this->assertInstanceOf('EasyWeChat\Work\Application', $work);
        $this->assertInstanceOf(\EasyWeChat\Work\OA\Client::class, $work->oa);
        $this->assertInstanceOf(\EasyWeChat\Work\Agent\Client::class, $work->agent);
        $this->assertInstanceOf(\EasyWeChat\Work\Chat\Client::class, $work->chat);
        $this->assertInstanceOf(\EasyWeChat\Work\Department\Client::class, $work->department);
        $this->assertInstanceOf(\EasyWeChat\Work\Media\Client::class, $work->media);
        $this->assertInstanceOf(\EasyWeChat\Work\Menu\Client::class, $work->menu);
        $this->assertInstanceOf(\EasyWeChat\Work\Message\Client::class, $work->message);
        $this->assertInstanceOf(\EasyWeChat\Work\Message\Messenger::class, $work->messenger);
        $this->assertInstanceOf(\EasyWeChat\Work\Server\Guard::class, $work->server);
        $this->assertInstanceOf(\EasyWeChat\BasicService\Jssdk\Client::class, $work->jssdk);
        $this->assertInstanceOf(\EasyWeChat\Work\OAuth\Manager::class, $work->oauth);
    }

    public function testDynamicCalls()
    {
        $app = new Application(['corp_id' => 'mock-corp-id']);
        $app['base'] = new class() {
            public function dummyMethod()
            {
                return 'mock-result';
            }
        };

        $this->assertSame('mock-result', $app->dummyMethod());
    }
}
