<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Agent\Client;
use EasyWeChat\Work\AgentFactory;
use EasyWeChat\Work\Application;
use Symfony\Component\HttpFoundation\Request;

class AgentFactoryTest extends TestCase
{
    public function testMake()
    {
        $factory = new AgentFactory([
            'corp_id' => 'mock-corp-id',
            'agents' => [
                'foo' => [
                    'agend_id' => 123,
                    'secret' => 'aabb',
                ],
                'bar' => [
                    'agend_id' => 124,
                    'secret' => 'aabb',
                ],
            ],
        ], ['foo' => 'bar']);

        $app = $factory->make('foo');

        $this->assertInstanceOf(Application::class, $app);
        $this->assertSame('mock-corp-id', $app['config']['corp_id']);
        $this->assertSame(123, $app['config']['agend_id']);
        $this->assertSame('aabb', $app['config']['secret']);
        $this->assertSame('bar', $app['foo']);

        // agent
        $this->assertSame($app, $factory->agent('foo'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No agent named "contacts".');

        $factory->make('contacts');
    }

    public function testAgentArrayAccess()
    {
        $factory = new AgentFactory([
            'corp_id' => 'mock-corp-id',
            'agents' => [
                'foo' => [
                    'agend_id' => 123,
                    'secret' => 'aabb',
                ],
                'bar' => [
                    'agend_id' => 124,
                    'secret' => 'aabb',
                ],
            ],
        ], ['foo' => 'bar']);

        $request = \Mockery::mock(Request::class);

        $this->assertInstanceOf(\ArrayAccess::class, $factory);

        $factory['request'] = $request;
        $app = $factory->make('foo');

        $this->assertSame($request, $factory['request']);
        $this->assertTrue(isset($factory['request']));
        unset($factory['request']);
        $this->assertFalse(isset($factory['request']));
        $this->assertSame($app, $factory['foo']);
    }

    public function testAgentProperty()
    {
        $factory = new AgentFactory([
            'corp_id' => 'mock-corp-id',
            'agents' => [
                'foo' => [
                    'agend_id' => 123,
                    'secret' => 'aabb',
                ],
                'bar' => [
                    'agend_id' => 124,
                    'secret' => 'aabb',
                ],
            ],
        ], ['foo' => 'bar']);

        $app = $factory->make('foo');

        $this->assertInstanceOf(Client::class, $app->agent);
    }

    public function testDefaultAgent()
    {
        $factory = new AgentFactory([
            'corp_id' => 'mock-corp-id',
            'agents' => [
                'foo' => [
                    'agend_id' => 123,
                    'secret' => 'aabb',
                ],
                'bar' => [
                    'agend_id' => 124,
                    'secret' => 'aabb',
                ],
            ],
        ], ['foo' => 'bar']);

        $foo = $factory->make('foo');

        $this->assertSame($foo->oa, $factory->oa);
    }

    public function testDefaultAgentWithConfiguredName()
    {
        $factory = new AgentFactory([
            'corp_id' => 'mock-corp-id',
            'default_agent' => 'bar',
            'agents' => [
                'foo' => [
                    'agend_id' => 123,
                    'secret' => 'aabb',
                ],
                'bar' => [
                    'agend_id' => 124,
                    'secret' => 'aabb',
                ],
            ],
        ], ['foo' => 'bar']);

        $bar = $factory->make('bar');

        $this->assertSame($bar->oa, $factory->oa);
    }
}
