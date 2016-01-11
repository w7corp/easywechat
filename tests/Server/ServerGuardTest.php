<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Encryption\Encryptor;
use EasyWeChat\Message\Raw;
use EasyWeChat\Server\Guard;
use EasyWeChat\Support\XML;
use Symfony\Component\HttpFoundation\Request;

class ServerGuardTest extends PHPUnit_Framework_TestCase
{
    public function getServer($message = '', $queries = null)
    {
        $request = Mockery::mock(Request::class.'[get,getContent]');

        $request->shouldReceive('get')->andReturnUsing(function ($key) use ($queries) {
            $queries = $queries ?: [
                'signature' => '5fe39987c51aa87c0da1af7420d4649d77850391',
                'timestamp' => '1437865042',
                'nonce' => '335941714',
            ];

            return isset($queries[$key]) ? $queries[$key] : null;
        });

        $message = $message ?: [
                'ToUserName' => 'gh_9a1a7e312b32',
                'FromUserName' => 'oNlnUjq_uJdd52zt3OxFsJHEr_NY',
                'CreateTime' => '1437865042',
                'MsgType' => 'text',
                'Content' => 'foobar',
                'MsgId' => '6175583331658476609',
            ];

        $request->shouldReceive('getContent')->andReturn(XML::build($message));

        $server = new Guard($request);

        return $server;
    }

    /**
     * Test server().
     */
    public function testServe()
    {
        $server = $this->getServer(null, [
                'echostr' => 'foobar',
            ]);

        $this->assertEquals('foobar', $server->serve()->getContent());

        $server = $this->getServer();

        $this->assertEquals(Guard::SUCCESS_EMPTY_RESPONSE, $server->serve()->getContent());
    }

    /**
     * Test response().
     */
    public function testStringResponse()
    {
        $server = $this->getServer();

        $server->setMessageHandler(function () {
            return 'hello world!';
        });

        $this->assertContains('hello world!', $server->serve()->getContent());
    }

    /**
     * Test response() with encrypted Request.
     */
    public function testResponseWithEncryptedRequest()
    {
        $server = $this->getServer(null, ['encrypt_type' => 'aes']);

        $server->setMessageHandler(function () {
            return 'hello world!';
        });

        $encryptor = Mockery::mock(Encryptor::class);
        $raw = null;
        $encryptor->shouldReceive('encryptMsg')->andReturnUsing(function ($message) use (&$raw) {
            $raw = $message;

            return base64_encode($message);
        });
        $encryptor->shouldReceive('decryptMsg')->andReturn([
                'FromUserName' => 'oNlnUjq_uJdd52zt3OxFsJHEr_NY',
                'ToUserName' => 't3OxFsJHEr_NY',
                'CreateTime' => '1437865042',
                'MsgType' => 'text',
                'Content' => 'foobar',
                'MsgId' => '6175583331658476609',
            ]);
        $server->setEncryptor($encryptor);

        $response = $server->serve()->getContent();

        $this->assertEquals(base64_encode($raw), $response);
    }

    /**
     * Test response() with event.
     */
    public function testResponseWithEvent()
    {
        $server = $this->getServer([
                'ToUserName' => 'gh_9a1a7e312b32',
                'FromUserName' => 'oNlnUjq_uJdd52zt3OxFsJHEr_NY',
                'CreateTime' => '1437865042',
                'MsgType' => 'event',
                'Event' => 'subscribe',
            ]);
        $logEvent = null;
        $response = $server->serve();
        $this->assertEquals(Guard::SUCCESS_EMPTY_RESPONSE, $response->getContent());

        //with listener
        $server->setMessageHandler(function ($event) use (&$logEvent) {
            $logEvent = $event;

            return $event->get('Event');
        });

        $response = $server->serve();

        $this->assertEquals('subscribe', $logEvent->get('Event'));
        $this->assertContains('subscribe', $response->getContent());
    }

    /**
     * Test setMessageHandler().
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testSetMessageHandler()
    {
        $server = $this->getServer();
        $closure = function () { return 'foo'; };
        $server->setMessageHandler($closure);

        $this->assertEquals($closure, $server->getMessageHandler());

        $server->setMessageHandler('Foo::bar');

        $this->assertEquals('Foo::bar', $server->getMessageHandler());

        $server->setMessageHandler('foo'); // invalid
    }

    /**
     * Test return raw message.
     */
    public function testRawMessage()
    {
        $server = $this->getServer();

        $string = '{
            "touser":"OPENID",
            "msgtype":"text",
            "text":
            {
                 "content":"Hello World"
            }
        }';
        $message = new Raw($string);

        $server->setMessageHandler(function () use ($message) {
            return $message;
        });

        $this->assertContains($string, $server->serve()->getContent());
    }

    /**
     * Test response() with different msg type.
     */
    public function testResponseWithDifferentMsgType()
    {
        $server = $this->getServer([
                'ToUserName' => 'gh_9a1a7e312b32',
                'FromUserName' => 'oNlnUjq_uJdd52zt3OxFsJHEr_NY',
                'CreateTime' => '1437865042',
                'MsgType' => 'text',
                'Content' => 'hello',
            ]);
        $logger = null;
        $response = $server->serve();
        $this->assertEquals(Guard::SUCCESS_EMPTY_RESPONSE, $response->getContent());

        //with all
        $server->setMessageHandler(function ($message) use (&$logger) {
            $logger = $message;

            return $message->get('Content');
        });

        $response = $server->serve();

        $this->assertEquals('hello', $logger->get('Content'));
        $this->assertContains('hello', $response->getContent());

        //with image
        $server->setMessageHandler(function ($message) use (&$logger) {
            $logger = $message;

            return $message->get('Content');
        }, Guard::ALL_MSG);

        $response = $server->serve();
        $this->assertEquals('hello', $logger->get('Content'));
        $this->assertContains('hello', $response->getContent());

        //with text
        $server->setMessageHandler(function ($message) use (&$logger) {
            $logger = $message;

            return $message->get('Content');
        }, Guard::TEXT_MSG);

        $response = $server->serve();
        $this->assertEquals('hello', $logger->get('Content'));
        $this->assertContains('hello', $response->getContent());

        //with image
        $server->setMessageHandler(function ($message) use (&$logger) {
            $logger = $message;

            return $message->get('Content');
        }, Guard::IMAGE_MSG);

        $response = $server->serve();
        $this->assertEquals(Guard::SUCCESS_EMPTY_RESPONSE, $response->getContent());

        // except image
        $server->setMessageHandler(function ($message) use (&$logger) {
            $logger = $message;

            return $message->get('Content');
        }, Guard::ALL_MSG & ~Guard::TEXT_MSG);

        $response = $server->serve();
        $this->assertEquals(Guard::SUCCESS_EMPTY_RESPONSE, $response->getContent());
    }
}

class Foo
{
    public function bar()
    {
        return 'foobar';
    }
}
