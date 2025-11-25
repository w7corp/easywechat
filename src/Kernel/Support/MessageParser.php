<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Support;

use EasyWeChat\Kernel\Exceptions\BadRequestException;

class MessageParser
{
    /**
     * Parse message content from string (XML or JSON).
     * This is a pure parser that automatically detects and parses XML or JSON format.
     *
     * @return array<string, mixed>
     *
     * @throws BadRequestException
     */
    public static function parse(string $content): array
    {
        $content = trim($content);

        // Try JSON format first
        $parsed = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($parsed) && ! empty($parsed)) {
            /** @var array<string, mixed> $parsed */
            return $parsed;
        }

        // If JSON decode failed or result is not an array, try XML format
        $parsed = Xml::parse($content);

        if (is_array($parsed) && ! empty($parsed)) {
            /** @var array<string, mixed> $parsed */
            return $parsed;
        }

        throw new BadRequestException('Failed to decode content. Content must be valid XML or JSON.');
    }
}
