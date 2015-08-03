<?php

use EasyWeChat\Server\Guard;
use EasyWeChat\Server\Transformer;

class ServerGuardTest extends TestCase
{
    /**
     * Test server()
     */
    public function testServe()
    {
        $input = Mockery::mock('EasyWeChat\Core\Input');
        $input->shouldReceive('has')->andReturn(true);
        $input->shouldReceive('get')->andReturn('foobar');

        $server = new Guard($input, Mockery::mock('EasyWeChat\Encryption\Cryptor'), new Transformer());

        $this->assertEquals('foobar', $server->serve());

        $input = Mockery::mock('EasyWeChat\Core\Input');
        $input->shouldReceive('isEncrypted')->andReturn(false);
        $input->shouldReceive('has')->andReturnUsing(function($key){
            return $key != 'echostr';
        });

        $input->shouldReceive('get')->andReturnUsing(function($key){
            $message = [
                "signature" => "5fe39987c51aa87c0da1af7420d4649d77850391",
                "timestamp" => "1437865042",
                "nonce" => "335941714",
                "ToUserName" => "gh_9a1a7e312b32",
                "FromUserName" => "oNlnUjq_uJdd52zt3OxFsJHEr_NY",
                "CreateTime" => "1437865042",
                "MsgType" => "text",
                "Content" => "foobar",
                "MsgId" => "6175583331658476609"
            ];


            return $message[$key];
        });

        $server = new Guard($input, Mockery::mock('EasyWeChat\Encryption\Cryptor'), new Transformer());

        $this->assertEquals('', $server->serve());
    }

    /**
     * Test response()
     */
    public function testStringResponse()
    {
        $input = Mockery::mock('EasyWeChat\Core\Input');
        $input->shouldReceive('isEncrypted')->andReturn(false);
        $input->shouldReceive('has')->andReturnUsing(function($key){
            return $key != 'echostr';
        });

        $input->shouldReceive('get')->andReturnUsing(function($key){
            $message = [
                "signature" => "5fe39987c51aa87c0da1af7420d4649d77850391",
                "timestamp" => "1437865042",
                "nonce" => "335941714",
                "ToUserName" => "gh_9a1a7e312b32",
                "FromUserName" => "oNlnUjq_uJdd52zt3OxFsJHEr_NY",
                "CreateTime" => "1437865042",
                "MsgType" => "text",
                "Content" => "foobar",
                "MsgId" => "6175583331658476609"
            ];


            return $message[$key];
        });

        $server = new Guard($input, Mockery::mock('EasyWeChat\Encryption\Cryptor'), new Transformer());

        $server->setMessageListener(function(){
            return "hello world!";
        });

        $this->assertContains('hello world!', $server->serve());
    }

    /**
     * Test response() with encrypted input.
     */
    public function testResponseWithEncryptedRequest()
    {
        $input = Mockery::mock('EasyWeChat\Core\Input');
        $input->shouldReceive('isEncrypted')->andReturn(true);
        $input->shouldReceive('has')->andReturnUsing(function($key){
            return $key != 'echostr';
        });
        $input->shouldReceive('get')->andReturnUsing(function($key){
            $message = [
                "signature" => "5fe39987c51aa87c0da1af7420d4649d77850391",
                "timestamp" => "1437865042",
                "nonce" => "335941714",
                "ToUserName" => "gh_9a1a7e312b32",
                "FromUserName" => "oNlnUjq_uJdd52zt3OxFsJHEr_NY",
                "CreateTime" => "1437865042",
                "MsgType" => "text",
                "Content" => "foobar",
                "MsgId" => "6175583331658476609"
            ];


            return $message[$key];
        });
        $encryptor = Mockery::mock('EasyWeChat\Encryption\Cryptor');
        $raw = null;
        $encryptor->shouldReceive('encryptMsg')->andReturnUsing(function($message) use(&$raw)
        {
            $raw = $message;
            return base64_encode($message);
        });

        $server = new Guard($input, $encryptor, new Transformer());

        $server->setMessageListener(function(){
            return "hello world!";
        });

        $response = $server->serve();

        $this->assertEquals(base64_encode($raw), $response);
    }

    /**
     * Test response() with event.
     */
    public function testResponseWithEvent()
    {
        $input = Mockery::mock('EasyWeChat\Core\Input');
        $input->shouldReceive('isEncrypted')->andReturn(false);
        $input->shouldReceive('has')->andReturnUsing(function($key){
            return $key != 'echostr';
        });
        $input->shouldReceive('get')->andReturnUsing(function($key){
            $message = [
                "signature" => "5fe39987c51aa87c0da1af7420d4649d77850391",
                "timestamp" => "1437865042",
                "nonce" => "335941714",
                "ToUserName" => "gh_9a1a7e312b32",
                "FromUserName" => "oNlnUjq_uJdd52zt3OxFsJHEr_NY",
                "CreateTime" => "1437865042",
                "MsgType" => "event",
                "Event" => "subscribe",
            ];

            return $message[$key];
        });
        $encryptor = Mockery::mock('EasyWeChat\Encryption\Cryptor');

        $server = new Guard($input, $encryptor, new Transformer());
        $logEvent = null;
        $response = $server->serve();
        $this->assertEquals('', $response);

        //with listener
        $server->setEventListener(function($event) use (&$logEvent){
            $logEvent = $event;
            return $event->get('Event');
        });

        $response = $server->serve();

        $this->assertEquals('subscribe', $logEvent->get('Event'));
        $this->assertContains('subscribe', $response);
    }

    /**
     * Test setEventListener()
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testSetEventListener()
    {
        $server = new Guard(Mockery::mock('EasyWeChat\Core\Input'), Mockery::mock('EasyWeChat\Encryption\Cryptor'), new Transformer());
        $closure = function(){ return 'foo'; };
        $server->setEventListener($closure);

        $this->assertEquals($closure, $server->getEventListener());

        $server->setEventListener('Foo::bar');

        $this->assertEquals("Foo::bar", $server->getEventListener());
        
        $server->setEventListener('foo');// invalid
    }

    /**
     * Test setMessageListener()
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testSetMessageListener()
    {
        $server = new Guard(Mockery::mock('EasyWeChat\Core\Input'), Mockery::mock('EasyWeChat\Encryption\Cryptor'), new Transformer());
        $closure = function(){ return 'foo'; };
        $server->setMessageListener($closure);

        $this->assertEquals($closure, $server->getMessageListener());

        $server->setMessageListener('Foo::bar');

        $this->assertEquals("Foo::bar", $server->getMessageListener());

        $server->setMessageListener('foo');// invalid
    }
}

class Foo {
    public function bar()
    {
        return 'foobar';
    }
}