<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Pay\Message;
use EasyWeChat\Tests\TestCase;
use RuntimeException;

class MessageTest extends TestCase
{
    public function test_get_event_type()
    {
        $message = new Message([], '{"event_type":"TRANSACTION.SUCCESS"}');

        $this->assertSame('TRANSACTION.SUCCESS', $message->getEventType());
    }

    public function test_missing_event_type_throws_without_warnings()
    {
        $message = new Message([], '{}');
        $errors = [];

        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            try {
                $message->getEventType();
                $this->fail('Expected getEventType() to throw.');
            } catch (RuntimeException $e) {
                $this->assertSame('Invalid event type.', $e->getMessage());
            }
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }
}
