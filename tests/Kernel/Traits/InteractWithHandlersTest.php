<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use PHPUnit\Framework\TestCase;

class InteractWithHandlersTest extends TestCase
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function test_it_has_callable_handlers()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $c = new class() {
            public function hello()
            {
            }
        };
        $m->with([$c, 'hello']);
        $this->assertCount(1, $m->getHandlers());

        // remove
        $m->withoutHandler([$c, 'hello']);
        $this->assertCount(0, $m->getHandlers());
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

        // remove
        $m->withoutHandler($h);
        $this->assertCount(0, $m->getHandlers());
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function test_it_has_class_based_handlers()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $m->with(DummyClassBasedHandler::class);
        $this->assertCount(1, $m->getHandlers());

        // remove
        $m->withoutHandler(DummyClassBasedHandler::class);
        $this->assertCount(0, $m->getHandlers());
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
        $m->unless(fn () => false, $h4);

        $this->assertCount(3, $m->getHandlers());
        $this->assertFalse($m->has($h1));
        $this->assertTrue($m->has($h2));
        $this->assertTrue($m->has($h3));
        $this->assertTrue($m->has($h4));
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function test_it_can_handle_with_chain_handles()
    {
        $m = \Mockery::mock(InteractWithHandlers::class);

        $h1 = function ($payload, $next) use (&$log) {
            return $next($payload);
        };

        $h2 = function ($payload, $next) use (&$log) {
            return $next($payload);
        };

        $h3 = function ($payload, $next) use (&$log) {
            return "final result";
        };

        $h4 = function ($payload, $next) use (&$log) {
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
}

class DummyClassBasedHandler
{
    public function __invoke($payload, \Closure $next)
    {
        return 'hello';
    }
}
