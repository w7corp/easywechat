<?php


use EasyWeChat\Core\Http;
use EasyWeChat\Message\Text;
use EasyWeChat\Staff\Messenger;
use EasyWeChat\Staff\Transformer;

class StaffMessengerTest extends TestCase
{
    public function getMessenger()
    {
        $http = Mockery::mock(Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function($api, $message){
            return compact('api', 'message');
        });

        $transformer = Mockery::mock(Transformer::class);
        $transformer->shouldReceive('transform')->andReturnUsing(function($message){
            return $message;
        });

        $messenger = new Messenger($http, $transformer);

        return $messenger;
    }

    /**
     * Test message()
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testMessage()
    {
        $messenger = $this->getMessenger();

        $response = $messenger->message('hello');

        $this->assertEquals($messenger, $response);
        $this->assertInstanceOf(Text::class, $messenger->message);

        // exception
        $messenger->message(new stdClass());
    }

    /**
     * Test by()
     */
    public function testBy()
    {
        $messenger = $this->getMessenger();

        $response = $messenger->by('hello');

        $this->assertEquals($messenger, $response);
        $this->assertEquals('hello', $messenger->account);
        $this->assertNull($messenger->by);
    }

    /**
     * Test to()
     */
    public function testTo()
    {
        $messenger = $this->getMessenger();

        $response = $messenger->to('overtrue');

        $this->assertEquals($messenger, $response);
        $this->assertEquals('overtrue', $messenger->to);
    }

    /**
     * Test send()
     *
     * @expectedException EasyWeChat\Core\Exceptions\RuntimeException
     */
    public function testSend()
    {
        $messenger = $this->getMessenger();

        $response = $messenger->message('hello')->by('overtrue')->to('easywechat')->send();

        $this->assertEquals(Messenger::API_MESSAGE_SEND, $response['api']);
        $this->assertEquals('text', $response['message']['msgtype']);
        $this->assertEquals('hello', $response['message']['text']['content']);
        $this->assertEquals('overtrue', $response['message']['customservice']['kf_account']);
        $this->assertEquals('easywechat', $response['message']['touser']);

        // exception
        $messenger = $this->getMessenger();
        $messenger->by('overtrue')->to('easywechat')->send();
    }
}