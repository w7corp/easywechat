<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\Traits\RespondXmlMessage;
use EasyWeChat\Tests\TestCase;

class RespondXmlMessageTest extends TestCase
{
    use RespondXmlMessage;

    public function test_it_will_return_success_response()
    {
        $response = $this->transformToReply('', \Mockery::mock(Message::class));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', \strval($response->getBody()));
    }

    public function test_it_will_handle_array_response()
    {
        $response = $this->transformToReply([
            'MsgType' => 'text',
            'Content' => 'Hello',
        ], \Mockery::mock(Message::class));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertMatchesRegularExpression(
            '~<xml>'
                    .'<CreateTime>\d{10}</CreateTime>'
                    .'<MsgType>text</MsgType>'
                    .'<Content>Hello</Content>'
                    .'</xml>~',
            \strval($response->getBody())
        );
    }

    public function test_it_will_handle_string_response()
    {
        $response = $this->transformToReply('Hello', \Mockery::mock(Message::class));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/xml', $response->getHeaderLine('content-type'));
        $this->assertMatchesRegularExpression(
            '~<xml>'
            .'<CreateTime>\d{10}</CreateTime>'
            .'<MsgType>text</MsgType>'
            .'<Content>Hello</Content>'
            .'</xml>~',
            \strval($response->getBody())
        );
    }

    public function test_it_will_throw_when_response_type_error()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Response type "object".');
        $this->transformToReply(new \stdClass(), \Mockery::mock(Message::class));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Response type "boolean".');
        $this->transformToReply(false, \Mockery::mock(Message::class));
    }
}
