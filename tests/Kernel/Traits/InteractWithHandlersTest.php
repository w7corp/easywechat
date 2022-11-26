<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Tests\TestCase;

class InteractWithHandlersTest extends TestCase
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function test_it_has_callable_handlers()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $c = new class()
        {
            public function hello()
            {
                return 'hello';
            }
        };
        $m->with([$c, 'hello']);
        $this->assertCount(1, $m->getHandlers());
        $this->assertSame('hello', $m->handle('result'));

        // remove
        $m->withoutHandler([$c, 'hello']);

        $ci = new class()
        {
            public function __invoke()
            {
                return 'hello invoke';
            }
        };
        $m->with($ci);
        $this->assertCount(1, $m->getHandlers());
        $this->assertSame('hello invoke', $m->handle('result'));

        // remove
        $m->withoutHandler($ci);

        $this->assertCount(0, $m->getHandlers());

        $this->assertSame('result', $m->handle('result'));
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function test_it_has_closure_handlers()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $h = fn () => 'hello';
        $m->with($h);
        $this->assertCount(1, $m->getHandlers());
        $this->assertSame('hello', $m->handle('result'));

        // remove
        $m->withoutHandler($h);
        $this->assertCount(0, $m->getHandlers());
        $this->assertSame('result', $m->handle('result'));
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function test_it_has_class_based_handlers()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $m->with(DummyClassBasedHandler::class);
        $this->assertCount(1, $m->getHandlers());
        $this->assertSame('hello', $m->handle('result'));

        // remove
        $m->withoutHandler(DummyClassBasedHandler::class);

        $this->assertCount(0, $m->getHandlers());
        $this->assertSame('result', $m->handle('result'));
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function test_it_will_run_by_sort()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $h1 = function ($payload, $next) {
            return 'h1'.$next($payload);
        };

        $h2 = function ($payload, $next) {
            return 'h2'.$next($payload);
        };

        $h3 = function ($payload, $next) {
            return 'h3'.$next($payload);
        };

        $h4 = function ($payload, $next) {
            return 'h4';
        };

        $m->with($h1);
        $m->with($h2);
        $m->with($h3);
        $m->with($h4);
        $this->assertSame('h1h2h3h4', $m->handle('success'));
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function test_it_can_push_with_conditions()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $h1 = fn () => 'handler1';
        $h2 = fn () => 'handler2';
        $h3 = fn () => 'handler3';
        $h4 = fn () => 'handler4';

        $m->when(fn () => false, $h1);
        $m->when(fn () => true, $h2);
        $m->when('bool-value-true', $h3);
        $m->when(fn () => 0, $h4);

        $this->assertCount(2, $m->getHandlers());
        $this->assertFalse($m->has($h1));
        $this->assertTrue($m->has($h2));
        $this->assertTrue($m->has($h3));
        $this->assertFalse($m->has($h4));
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function test_it_can_handle_with_chain_handles()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $h1 = function ($payload, $next) {
            return $next($payload);
        };

        $h2 = function ($payload, $next) {
            return $next($payload);
        };

        $h3 = function ($payload, $next) {
            return 'final result';
        };

        $h4 = function ($payload, $next) {
            return $next($payload);
        };

        // h4 will not run
        $m->with($h1);
        $m->with($h2);
        $m->with($h3);
        $m->with($h4);
        $this->assertSame('final result', $m->handle('SUCCESS'));

        $m->without($h3);
        $this->assertSame('SUCCESS', $m->handle('SUCCESS'));
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function test_it_can_handle_with_default_value()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $h1 = function ($payload, $next) {
            return $next($payload);
        };

        $h2 = function ($payload, $next) {
            return $next($payload);
        };

        $h3 = function ($payload, $next) {
            return null;
        };

        $h4 = function ($payload, $next) {
            return 'hello';
        };

        $m->with($h1);
        $m->with($h2);
        $m->with($h3);
        $m->with($h4);

        // null
        $this->assertSame('default value', $m->handle('default value'));
        // closure
        $h5 = fn () => 'h5';
        $this->assertSame('h5', $m->handle($h5));

        // return $h4
        $m->without($h3);
        $this->assertSame('hello', $m->handle('default value'));
    }

    public function test_it_can_prepend_handlers()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $h1 = function ($payload, $next) {
            return 'h1'.$next($payload);
        };

        $h2 = function ($payload, $next) {
            return 'h2'.$next($payload);
        };

        $h3 = function ($payload, $next) {
            return 'h3'.$next($payload);
        };

        $h4 = function ($payload, $next) {
            return 'h4';
        };

        $m->with($h1);
        $m->with($h4);
        $this->assertSame('h1h4', $m->handle('success'));

        $m->prepend($h2);
        $this->assertSame('h2h1h4', $m->handle('success'));

        $m->prepend($h3);
        $this->assertSame('h3h2h1h4', $m->handle('success'));
    }
}

class DummyClassBasedHandler
{
    public function __invoke($payload, \Closure $next)
    {
        return 'hello';
    }
}
