<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Tests\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class InteractWithServerRequestTest extends TestCase
{
    public function test_get_and_set_request()
    {
        $app = new DummyClassForInteractWithServerRequestTest();

        $this->assertInstanceOf(ServerRequestInterface::class, $app->getRequest());
        $this->assertSame($app->getRequest(), $app->getRequest());

        // set
        $request = \Mockery::mock(ServerRequestInterface::class);
        $app->setRequest($request);
        $this->assertSame($request, $app->getRequest());
    }
}

class DummyClassForInteractWithServerRequestTest
{
    use InteractWithServerRequest;
}
