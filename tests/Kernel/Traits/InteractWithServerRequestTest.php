<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Tests\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class InteractWithServerRequestTest extends TestCase
{
    public function test_get_and_set_request()
    {
        $app = new DummyClassForInteractWithServerRequestTest;

        $this->assertInstanceOf(ServerRequestInterface::class, $app->getRequest());
        $this->assertSame($app->getRequest(), $app->getRequest());

        // set
        $request = \Mockery::mock(ServerRequestInterface::class);
        $app->setRequest($request);
        $this->assertSame($request, $app->getRequest());
    }

    public function test_it_can_set_request_from_symfony_request()
    {
        $app = new DummyClassForInteractWithServerRequestTest;

        $request = \Symfony\Component\HttpFoundation\Request::create('/foo', 'GET');

        $app->setRequestFromSymfonyRequest($request);

        $this->assertInstanceOf(ServerRequestInterface::class, $app->getRequest());
        $this->assertSame($request->getUri(), \strval($app->getRequest()->getUri()));
        $this->assertSame($request->getMethod(), $app->getRequest()->getMethod());
    }
}

class DummyClassForInteractWithServerRequestTest
{
    use InteractWithServerRequest;
}
