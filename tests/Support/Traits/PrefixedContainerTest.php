<?php

namespace EasyWeChat\Tests\Support\Traits;

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
            'application.foo' => 'foo@application'
        ]);

        $this->assertEquals('foo@application', $app->fetch('foo'));
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
            'application.bar' => 'bar@application'
        ]);

        $this->assertEquals('bar@application', $app->fetch('bar'));
    }
}

class Application
{
    use \EasyWeChat\Support\Traits\PrefixedContainer;
}
