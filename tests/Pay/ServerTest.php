<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Pay\Contracts\Merchant;
use EasyWeChat\Pay\Server;
use EasyWeChat\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;

class ServerTest extends TestCase
{
    public function test_it_will_handle_validation_request()
    {
        $request = (new ServerRequest(
            'POST',
            'http://easywechat.com/',
            [],
            fopen(__DIR__.'/../fixtures/files/pay_demo.json', 'r')
        ));

        $merchant = \Mockery::mock(Merchant::class);
        $merchant->shouldReceive('getSecretKey')->andReturn('key');

        $server = new Server($merchant, $request);

        $response = $server->serve();
        $this->assertSame('{"code":"SUCCESS","message":"成功"}', \strval($response->getBody()));
    }
}
