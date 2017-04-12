<?php

/**
 * Test GuardTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\Foundation\Application;
use EasyWeChat\OpenPlatform\Guard;
use EasyWeChat\Tests\TestCase;

class GuardTest extends TestCase
{
    public function testGetHandler()
    {
        $server = $this->make();

        $handlers = [
            Guard::EVENT_AUTHORIZED => 'EasyWeChat\OpenPlatform\EventHandlers\Authorized',
            Guard::EVENT_UNAUTHORIZED => 'EasyWeChat\OpenPlatform\EventHandlers\Unauthorized',
            Guard::EVENT_UPDATE_AUTHORIZED => 'EasyWeChat\OpenPlatform\EventHandlers\UpdateAuthorized',
            Guard::EVENT_COMPONENT_VERIFY_TICKET => 'EasyWeChat\OpenPlatform\EventHandlers\ComponentVerifyTicket',
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

        $app = new Application($config);

        return $app->open_platform->server;
    }
}
