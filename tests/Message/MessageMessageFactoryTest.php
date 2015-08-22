<?php

use EasyWeChat\Core\Application;
use EasyWeChat\Message\MessageFactory;

class MessageMessageFactoryTest extends TestCase
{
    /**
     * Test make()
     */
    public function testMake()
    {
        $app = Mockery::mock(Application::class);
        $app->shouldReceive('get')->andReturnUsing(function($id){
            return $id;
        });
        $factory = new MessageFactory($app);

        $this->assertEquals('message.text', $factory->make('text'));
        $this->assertEquals('message.image', $factory->make('image'));
        $this->assertEquals('message.voice', $factory->make('voice'));
        $this->assertEquals('message.video', $factory->make('video'));
        $this->assertEquals('message.music', $factory->make('music'));
        $this->assertEquals('message.articles', $factory->make('articles'));
    }

    /**
     * Test make() with invalid type.
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testMakeWithInvalidType()
    {
        $app = Mockery::mock(Application::class);
        $factory = new MessageFactory($app);

        $factory->make('foo');
    }
}