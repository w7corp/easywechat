<?php

/**
 * Test OpenPlatformTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */
use EasyWeChat\Foundation\Application;
use EasyWeChat\OpenPlatform\Components\Authorizer;
use EasyWeChat\OpenPlatform\Components\PreAuthCode;
use EasyWeChat\OpenPlatform\Guard;

class OpenPlatformTest extends TestCase
{
    public function testOpenPlatform()
    {
        $app = $this->make();

        $this->assertInstanceOf(Authorizer::class, $app->open_platform->authorizer);
        $this->assertInstanceOf(PreAuthCode::class, $app->open_platform->pre_auth);
        $this->assertInstanceOf(Guard::class, $app->open_platform->server);
    }

    /**
     * Makes application.
     *
     * @return Application
     */
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

        return new Application($config);
    }
}
