<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Support\Traits;

use EasyWeChat\Tests\TestCase;
use Pimple\Container;

class PrefixedContainerTest extends TestCase
{
    protected function make(array $values = [])
    {
        return new Application(
            new Container($values)
        );
    }

    public function testFetch()
    {
        $app = $this->make([
            'application.foo' => 'foo@application',
        ]);

        $this->assertSame('foo@application', $app->fetch('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrownInvalidArgumentException()
    {
        $app = $this->make();

        $app->fetch('not-exists');
    }

    public function testMagicGet()
    {
        $app = $this->make([
            'application.bar' => 'bar@application',
        ]);

        $this->assertSame('bar@application', $app->bar);
    }
}

class Application
{
    use \EasyWeChat\Support\Traits\PrefixedContainer;
}
