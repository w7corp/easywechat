<?php

namespace EasyWeChat\Tests\Kernel;

use EasyWeChat\Pay\Message;
use EasyWeChat\Tests\TestCase;

class MessageTest extends TestCase
{
    public function test_message()
    {
        $message = new Message(['one' => 1]);
        $this->assertSame($message->one, 1);
        $this->assertSame($message['one'], 1);
    }

    public function test_message_can_be_encode_as_json()
    {
        $message = new Message(['one' => 1]);
        $this->assertSame(json_encode($message), '{"one":1}');
    }
}
