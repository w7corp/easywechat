<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Traits;

use Closure;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\Kernel\Decorators\TerminateResult;
use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Traits\Observable;
use EasyWeChat\Tests\TestCase;

class ObservableTest extends TestCase
{
    public function demoHandler()
    {
    }

    public function testCallHandler()
    {
        $c = new DummyClassForObservableTest();

        // handler interface
        $handler = \Mockery::mock(EventHandlerInterface::class);
        $c->push($handler);
        $this->assertArrayHasKey('*', $c->getHandlers());
        $this->assertInstanceOf(Closure::class, $c->getHandlers()['*'][0]);

        // callable
        $handler = [$this, 'demoHandler'];
        $c->push($handler);
        $this->assertSame($handler, $c->getHandlers()['*'][1]);

        // function
        $handler = function () {
        };
        $c->push($handler);
        $this->assertSame($handler, $c->getHandlers()['*'][2]);
    }

    public function testAddObserverWithoutEvent()
    {
        $c = new DummyClassForObservableTest();

        $handler = \Mockery::mock(EventHandlerInterface::class);
        $c->push($handler);
        $this->assertArrayHasKey('*', $c->getHandlers());
        $this->assertInstanceOf(Closure::class, $c->getHandlers()['*'][0]);

        $handler = \Mockery::mock(EventHandlerInterface::class);
        $c->push('*', $handler);
        $this->assertArrayHasKey('*', $c->getHandlers());
        $this->assertInstanceOf(Closure::class, $c->getHandlers()['*'][0]);

        // on
        $handler = \Mockery::mock(EventHandlerInterface::class);
        $c->on('foo', $handler);
        $this->assertArrayHasKey('foo', $c->getHandlers());
        $this->assertInstanceOf(Closure::class, $c->getHandlers()['foo'][0]);

        $handler = \Mockery::mock(EventHandlerInterface::class);
        $c->observe('foo', $handler);
        $this->assertArrayHasKey('foo', $c->getHandlers());
        $this->assertInstanceOf(Closure::class, $c->getHandlers()['foo'][0]);
    }

    public function testAddObserverWithEventName()
    {
        $c = new DummyClassForObservableTest();
        $handler1 = \Mockery::mock(EventHandlerInterface::class);
        $handler2 = \Mockery::mock(EventHandlerInterface::class);
        $c->push($handler1, 'foo');
        $c->push($handler2, 'foo');

        $this->assertArrayHasKey('foo', $c->getHandlers());
        $this->assertInternalType('array', $c->getHandlers()['foo']);
        $this->assertCount(1, $c->getHandlers());
        $this->assertCount(2, $c->getHandlers()['foo']);
        $this->assertInstanceOf(Closure::class, $c->getHandlers()['foo'][0]);
        $this->assertInstanceOf(Closure::class, $c->getHandlers()['foo'][1]);
    }

    public function testAddObserverWithEventNames()
    {
        $c = new DummyClassForObservableTest();
        $handler1 = \Mockery::mock(EventHandlerInterface::class);
        $handler2 = \Mockery::mock(EventHandlerInterface::class);
        $c->push($handler1, 'foo' | 'bar');
        $c->push($handler2, 'foo' | 'bar');

        $this->assertArrayHasKey('foo' | 'bar', $c->getHandlers());
        $this->assertInternalType('array', $c->getHandlers()['foo' | 'bar']);
        $this->assertCount(1, $c->getHandlers());
        $this->assertCount(2, $c->getHandlers()['foo' | 'bar']);
        $this->assertInstanceOf(Closure::class, $c->getHandlers()['foo' | 'bar'][0]);
        $this->assertInstanceOf(Closure::class, $c->getHandlers()['foo' | 'bar'][1]);
    }

    public function testUnshift()
    {
        $c = new DummyClassForObservableTest();
        $handler1 = \Mockery::mock(EventHandlerInterface::class);
        $c->push($handler1, 'foo');

        $this->assertCount(1, $c->getHandlers()['foo']);

        $handler2 = new DummyHandlerClassForObservableTest();
        $c->unshift($handler2, 'foo');
        $this->assertCount(2, $c->getHandlers()['foo']);
        $this->assertSame('handled', $c->getHandlers()['foo'][0](['foo' => 'bar']));

        // undefined index
        $c->unshift($handler2, 'bar');
        $this->assertCount(1, $c->getHandlers()['bar']);
        $this->assertSame('handled', $c->getHandlers()['bar'][0](['foo' => 'bar']));
    }

    public function testNotify()
    {
        $c = new DummyClassForObservableTest();
        $handler1 = \Mockery::mock(EventHandlerInterface::class);
        $handler1->expects()->handle(['foo' => 'bar'])->andReturn('mock-response');

        $handler2 = \Mockery::mock(EventHandlerInterface::class);
        $handler2->expects()->handle(['foo' => 'bar'])->andReturn(true);

        $handler3 = \Mockery::mock(EventHandlerInterface::class);
        $handler3->expects()->handle(['foo' => 'bar'])->andReturn('mock-response-2');

        $c->push($handler1);
        $c->push($handler2);
        $c->push($handler3);
        $this->assertSame('mock-response-2', $c->notify('foo', ['foo' => 'bar']));
    }

    public function testNotifyWithArrayCallableHandler()
    {
        $c = new DummyClassForObservableTest();
        $o = new DummyHandlerClass();
        $c->push([$o, 'handler4']);
        $c->push([$o, 'handler1'])->where('name', 'h1');
        $c->push([$o, 'handler2'])->where('name', 'h2');
        $c->push([$o, 'handler3'])->where('name', 'h3');

        $this->assertSame('handler1', $c->notify('foo', ['name' => 'h1']));
        $this->assertSame('handler2', $c->notify('foo', ['name' => 'h2']));
        $this->assertSame('handler3', $c->notify('foo', ['name' => 'h3']));
        $this->assertSame('handler4', $c->notify('foo', ['name' => 'not-exists']));
    }

    public function testNotifyWithStopPropagation()
    {
        $c = new DummyClassForObservableTest();
        $handler1 = \Mockery::mock(EventHandlerInterface::class);
        $handler1->expects()->handle(['foo' => 'bar'])->andReturn(null);

        $handler2 = \Mockery::mock(EventHandlerInterface::class);
        $handler2->expects()->handle(['foo' => 'bar'])->andReturn(false);

        $handler3 = \Mockery::mock(EventHandlerInterface::class);
        $handler3->expects()->handle(['foo' => 'bar'])->andReturn('mock-result')->never();

        $c->push($handler1);
        $c->push($handler2); // stop propagation
        $c->push($handler3);
        $this->assertNull($c->dispatch('foo', ['foo' => 'bar']));
    }

    public function testNotifyWithBitOperation()
    {
        $c = new DummyClassForObservableTest();
        $handler1 = \Mockery::mock(EventHandlerInterface::class);
        $handler1->allows()->handle(['foo' => 'bar'])->andReturn('handler1-response');

        $handler2 = \Mockery::mock(EventHandlerInterface::class);
        $handler2->allows()->handle(['foo' => 'bar'])->andReturn('handler2-response');

        $handler3 = \Mockery::mock(EventHandlerInterface::class);
        $handler3->allows()->handle(['foo' => 'bar'])->andReturn('handler3-response');

        $handler4 = \Mockery::mock(EventHandlerInterface::class);
        $handler4->allows()->handle(['foo' => 'bar'])->andReturn('handler4-response');

        $c->push($handler1);
        $c->push($handler1, 'event' | 'text');
        $c->push('image' | 'text', $handler2);
        $c->push('image', $handler3);
        $c->push('card', $handler3);
        $c->push('image' | 'text' | 'video', $handler4);
        $this->assertSame('handler1-response', $c->notify('not-exists', ['foo' => 'bar']));
        $this->assertSame('handler1-response', $c->notify('event', ['foo' => 'bar']));
        $this->assertSame('handler4-response', $c->notify('image', ['foo' => 'bar']));
        $this->assertSame('handler4-response', $c->notify('text', ['foo' => 'bar']));
        $this->assertSame('handler3-response', $c->notify('card', ['foo' => 'bar']));
    }

    public function testNotifyWithHandlerExceptionThrown()
    {
        $exception = new Exception('handler2 exception thrown.', -204);
        $line = __LINE__ - 1;
        $logger = \Mockery::mock('stdClass');
        $logger->expects()->error('-204: handler2 exception thrown.', [
            'code' => -204,
            'message' => 'handler2 exception thrown.',
            'file' => __FILE__,
            'line' => $line,
        ]);
        $app = new ServiceContainer([], [
            'logger' => $logger,
        ]);
        $c = new DummyClassForObservableTest($app);
        $handler1 = \Mockery::mock(EventHandlerInterface::class);
        $handler1->expects()->handle(['foo' => 'bar'])->andReturn('mock-response');

        $handler2 = \Mockery::mock(EventHandlerInterface::class);
        $handler2->expects()->handle(['foo' => 'bar'])->andThrow($exception);

        $handler3 = \Mockery::mock(EventHandlerInterface::class);
        $handler3->expects()->handle(['foo' => 'bar'])->andReturn('mock-response-3');

        $c->push($handler1);
        $c->push($handler2);
        $c->push($handler3);
        $this->assertSame('mock-response-3', $c->notify('foo', ['foo' => 'bar']));
    }

    public function testTerminateResultHandler()
    {
        $c = new DummyClassForObservableTest();
        $handler1 = \Mockery::mock(EventHandlerInterface::class);
        $handler1->expects()->handle(['foo' => 'bar'])->andReturn(new TerminateResult('mock-terminate-response'));

        $handler2 = \Mockery::mock(EventHandlerInterface::class);
        $handler2->expects()->handle(['foo' => 'bar'])->andThrow(new Exception('foo'))->never();

        $handler3 = \Mockery::mock(EventHandlerInterface::class);
        $handler3->expects()->handle(['foo' => 'bar'])->andReturn('mock-response-3')->never();

        $c->push($handler1);
        $c->push($handler2);
        $c->push($handler3);
        $this->assertSame('mock-terminate-response', $c->notify('foo', ['foo' => 'bar']));
    }

    public function testFinallyResultHandler()
    {
        $c = new DummyClassForObservableTest();

        $handler0 = \Mockery::mock(EventHandlerInterface::class);
        $handler0->expects()->handle(['foo' => 'bar'])->andReturn('mock-first-response');

        $handler1 = \Mockery::mock(EventHandlerInterface::class);
        $handler1->expects()->handle(['foo' => 'bar'])->andReturn(new FinallyResult('mock-finally-response'));

        $handler2 = \Mockery::mock(EventHandlerInterface::class);
        $handler2->expects()->handle(['foo' => 'bar'])->andReturn('mock-response-2');

        $handler3 = \Mockery::mock(EventHandlerInterface::class);
        $handler3->expects()->handle(['foo' => 'bar'])->andReturn('mock-response-3');

        $c->push($handler0);
        $c->push($handler1);
        $c->push($handler2);
        $c->push($handler3);
        $this->assertSame('mock-finally-response', $c->notify('foo', ['foo' => 'bar']));
    }

    public function testMakeClosure()
    {
        // closure
        $c = new DummyClassForObservableTest();
        $c->push(function () {
            return 'closure handler';
        }, 'foo');
        $this->assertSame('closure handler', $c->getHandlers()['foo'][0](['foo' => 'bar']));

        // class name
        $c = new DummyClassForObservableTest();
        $handler = new DummyHandlerClassForObservableTest();
        $c->push(get_class($handler), 'foo');
        $this->assertSame('handled', $c->getHandlers()['foo'][0](['foo' => 'bar']));

        // class instance
        $c = new DummyClassForObservableTest();
        $handler = \Mockery::mock(EventHandlerInterface::class);
        $handler->expects()->handle(['foo' => 'bar'])->andReturn('class instance handle');
        $c->push($handler, 'foo');
        $this->assertSame('class instance handle', $c->getHandlers()['foo'][0](['foo' => 'bar']));
    }

    public function testMakeClosureWithInvalidCases()
    {
        try {
            $c = new DummyClassForObservableTest();
            $c->push('invalid class name', 'foo');
        } catch (\Exception $e) {
            $this->assertSame('Class "foo" not exists.', $e->getMessage());
        }

        try {
            $c = new DummyClassForObservableTest();
            $c->push('stdClass', 'foo');
        } catch (\Exception $e) {
            $this->assertSame('Class "stdClass" not an instance of "EasyWeChat\Kernel\Contracts\EventHandlerInterface".', $e->getMessage());
        }

        try {
            $c = new DummyClassForObservableTest();
            $c->push(new \stdClass(), 'foo');
        } catch (\Exception $e) {
            $this->assertSame('No valid handler is found in arguments.', $e->getMessage());
        }
    }

    public function testWhereClause()
    {
        $c = new DummyClassForObservableTest();
        $handler1 = \Mockery::mock(EventHandlerInterface::class);
        $handler1->allows()->handle(['Type' => 'testing'])->andReturn('handler1-response');
        $c->push($handler1)->where('Type', 'staging');

        $this->assertNull($c->notify('foo', ['Type' => 'testing']));

        $c2 = new DummyClassForObservableTest();
        $handler2 = \Mockery::mock(EventHandlerInterface::class);
        $handler2->allows()->handle(['Type' => 'testing'])->andReturn('handler2-response');
        $c2->push($handler2)->where('Type', 'testing');

        $this->assertSame('handler2-response', $c2->notify('foo', ['Type' => 'testing']));

        $c3 = new DummyClassForObservableTest();
        $handler3 = \Mockery::mock(EventHandlerInterface::class);
        $handler3->allows()->handle(['Type' => 'testing', 'User' => 'user-123'])->andReturn('handler3-response');
        $c3->push($handler3)->where('Type', 'testing')->where('User', 'user-456');

        $this->assertNull($c3->notify('foo', ['Type' => 'testing', 'User' => 'user-123']));

        $c4 = new DummyClassForObservableTest();
        $handler4 = \Mockery::mock(EventHandlerInterface::class);
        $handler4->allows()->handle(['Type' => 'testing', 'User' => 'user-123'])->andReturn('handler4-response');
        $handler5 = \Mockery::mock(EventHandlerInterface::class);
        $handler5->allows()->handle(['Type' => 'bar', 'User' => 'bar-user'])->andReturn('handler5-response');
        $c4->push($handler4)->where('Type', 'foo');
        $c4->push($handler5)->where('Type', 'bar');

        $this->assertSame('handler5-response', $c4->notify('e', ['Type' => 'bar', 'User' => 'bar-user']));
    }
}

class DummyHandlerClassForObservableTest implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        return 'handled';
    }
}

class DummyClassForObservableTest
{
    protected $app;

    public function __construct(ServiceContainer $app = null)
    {
        $this->app = $app;
    }

    use Observable;
}

class DummyHandlerClass
{
    public function handler1($payload)
    {
        return 'handler1';
    }

    public function handler2($payload)
    {
        return 'handler2';
    }

    public function handler3($payload)
    {
        return 'handler3';
    }

    public function handler4($payload)
    {
        return 'handler4';
    }
}
