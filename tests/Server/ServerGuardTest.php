<?php

use Symfony\Component\HttpFoundation\Request;
use EasyWeChat\Encryption\Encryptor;
use EasyWeChat\Server\Guard;
use EasyWeChat\Server\Transformer;
use EasyWeChat\Support\XML;

class ServerGuardTest extends TestCase
{
    public function getServer($message = '', $queries = null)
    {
        $request = Mockery::mock(Request::class);

        $request->shouldReceive('get')->andReturnUsing(function ($key) use ($queries) {
            $queries = $queries ? : [
                'signature' => '5fe39987c51aa87c0da1af7420d4649d77850391',
                'timestamp' => '1437865042',
                'nonce' => '335941714',
            ];

            return isset($queries[$key]) ? $queries[$key] : null;
        });

        $message = $message ? : [
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
                'echostr' => 'foobar'
            ]);

        $this->assertEquals('foobar', $server->serve()->getContent());

        $server = $this->getServer();

        $this->assertEquals(Guard::EMPTY_STRING, $server->serve()->getContent());
    }

    /**
     * Test response().
     */
    public function testStringResponse()
    {
        $server = $this->getServer();

        $server->setMessageListener(function () {
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

        $server->setMessageListener(function () {
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
        $this->assertEquals(Guard::EMPTY_STRING, $response->getContent());

        //with listener
        $server->setEventListener(function ($event) use (&$logEvent) {
            $logEvent = $event;

            return $event->get('Event');
        });

        $response = $server->serve();

        $this->assertEquals('subscribe', $logEvent->get('Event'));
        $this->assertContains('subscribe', $response->getContent());
    }

    /**
     * Test setEventListener().
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testSetEventListener()
    {
        $server = $this->getServer();
        $closure = function () { return 'foo'; };
        $server->setEventListener($closure);

        $this->assertEquals($closure, $server->getEventListener());

        $server->setEventListener('Foo::bar');

        $this->assertEquals('Foo::bar', $server->getEventListener());

        $server->setEventListener('foo');// invalid
    }

    /**
     * Test setMessageListener().
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testSetMessageListener()
    {
        $server = $this->getServer();
        $closure = function () { return 'foo'; };
        $server->setMessageListener($closure);

        $this->assertEquals($closure, $server->getMessageListener());

        $server->setMessageListener('Foo::bar');

        $this->assertEquals('Foo::bar', $server->getMessageListener());

        $server->setMessageListener('foo');// invalid
    }
}

class Foo
{
    public function bar()
    {
        return 'foobar';
    }
}
