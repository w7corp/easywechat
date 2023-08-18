<?php

namespace EasyWeChat\Pay;

use RuntimeException;

use function is_array;
use function is_string;
use function json_decode;

/**
 * @property string $trade_state
 */
class Message extends \EasyWeChat\Kernel\Message
{
    /**
     * @return array<string, mixed>
     */
    public function getOriginalAttributes(): array
    {
        $attributes = json_decode($this->getOriginalContents(), true);

        return is_array($attributes) ? $attributes : [];
    }

    public function getEventType(): ?string
    {
        $eventType = $this->getOriginalAttributes()['event_type'];

        if (! is_string($eventType)) {
            throw new RuntimeException('Invalid event type.');
        }

        return $eventType;
    }
}
