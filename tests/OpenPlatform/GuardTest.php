<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Foundation\Application;
use EasyWeChat\OpenPlatform\EventHandlers\Authorized;
use EasyWeChat\OpenPlatform\EventHandlers\ComponentVerifyTicket;
use EasyWeChat\OpenPlatform\EventHandlers\EventHandler;
use EasyWeChat\OpenPlatform\EventHandlers\Unauthorized;
use EasyWeChat\OpenPlatform\EventHandlers\UpdateAuthorized;
use EasyWeChat\OpenPlatform\Guard;
use EasyWeChat\Support\Collection;

class GuardTest extends TestCase
{
    public function testGetHandler()
    {
        $server = $this->make();

        $handlers = [
            Guard::EVENT_AUTHORIZED => Authorized::class,
            Guard::EVENT_UNAUTHORIZED => Unauthorized::class,
            Guard::EVENT_UPDATE_AUTHORIZED => UpdateAuthorized::class,
            Guard::EVENT_COMPONENT_VERIFY_TICKET => ComponentVerifyTicket::class,
        ];

        foreach ($handlers as $type => $handler) {
            $this->assertInstanceOf($handler, $server->getHandlerForTest($type));
        }
    }

    public function testHandleMessage()
    {
        $server = $this->make();
        $result = $server->handleMessageForTest();

        $this->assertEquals(OpenPlatformGuardStub::$message, $result);
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

        $app = new Application($config);

        $server = new OpenPlatformGuardStub('token');
        $server->setContainer($app);

        $app['open_platform.handlers.test']
            = Mockery::mock(EventHandler::class)
                     ->shouldReceive('handle')
                     ->andReturnUsing(
                         function (Collection $message) {
                             return $message->all();
                         }
                     )
                     ->mock();

        return $server;
    }
}

class OpenPlatformGuardStub extends Guard
{
    public static $message = ['InfoType' => 'test'];

    public function getHandlerForTest($type)
    {
        return $this->getDefaultHandler($type);
    }

    public function handleMessageForTest()
    {
        return $this->handleMessage(self::$message);
    }
}
