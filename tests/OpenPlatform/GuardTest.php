<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\Applications\OpenPlatform\Server\Guard;
use EasyWeChat\Factory;
use EasyWeChat\Tests\TestCase;

class GuardTest extends TestCase
{
    public function testGetHandler()
    {
        $server = $this->make();

        $handlers = [
            Guard::EVENT_AUTHORIZED => 'EasyWeChat\Applications\OpenPlatform\Server\Handlers\Authorized',
            Guard::EVENT_UNAUTHORIZED => 'EasyWeChat\Applications\OpenPlatform\Server\Handlers\Unauthorized',
            Guard::EVENT_UPDATE_AUTHORIZED => 'EasyWeChat\Applications\OpenPlatform\Server\Handlers\UpdateAuthorized',
            Guard::EVENT_COMPONENT_VERIFY_TICKET => 'EasyWeChat\Applications\OpenPlatform\Server\Handlers\ComponentVerifyTicket',
        ];

        foreach ($handlers as $type => $handler) {
            $this->assertInstanceOf($handler, $server->getHandler($type));
        }
    }

    private function make()
    {
        $config = [
            'open_platform' => [
                'app_id' => 'your-app-id',
                'secret' => 'your-app-secret',
                'token' => 'your-token',
                'aes_key' => 'your-ase-key',
            ],
        ];

        $app = new Factory($config);

        return $app->offsetGet('open_platform.instance')->server;
    }
}
