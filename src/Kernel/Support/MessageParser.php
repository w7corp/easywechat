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
        // Try XML format if content starts with '<'
        if (stripos($content, '<') === 0) {
            $parsed = Xml::parse($content);

            if (is_array($parsed) && ! empty($parsed)) {
                return $parsed;
            }

            throw new BadRequestException('Failed to decode XML content.');
        }

        // Otherwise try JSON format
        $parsed = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && $content !== '' && is_array($parsed) && ! empty($parsed)) {
            return $parsed;
        }

        throw new BadRequestException('Failed to decode content. Content must be valid XML or JSON.');
    }
}
