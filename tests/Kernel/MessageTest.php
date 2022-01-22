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
}
