<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Support;

use InvalidArgumentException;
use SimpleXMLElement;
use function is_object;

class Xml
{
    /**
     * @return array<int|string,mixed>|null
     */
    public static function parse(string $xml): array|null
    {
        if (empty($xml)) {
            return null;
        }

        $xml = simplexml_load_string(
            self::sanitize($xml),
            'SimpleXMLElement',
            LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NOBLANKS
        );

        if (!$xml) {
            throw new InvalidArgumentException('Invalid XML');
        }

        return self::normalize($xml);
    }

    /**
     * @param  array<int|string, mixed>  $data
     * @param  string  $root
     * @param  string  $item
     * @param  string|array<string, mixed>  $attr
     * @param  string  $id
     *
     * @return string
     */
    public static function build(
        array $data,
        string $root = 'xml',
        string $item = 'item',
        string|array $attr = '',
        string $id = 'id'
    ): string {
        if (is_array($attr)) {
            $segments = [];

            foreach ($attr as $key => $value) {
                /** @phpstan-ignore-next-line */
                $segments[] = "{$key}=\"{$value}\"";
            }

            $attr = implode(' ', $segments);
        }

        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml .= self::data2Xml($data, $item, $id);
        $xml .= "</{$root}>";

        return $xml;
    }

    public static function cdata(?string $string): string
    {
        return sprintf('<![CDATA[%s]]>', $string ?? '');
    }

    /**
     * @psalm-suppress RedundantCondition
     * @param  array<SimpleXMLElement>|SimpleXMLElement  $object
     * @return array<int|string, mixed>|null
     */
    protected static function normalize(SimpleXMLElement|array $object): array|null
    {
        $result = null;

        if (is_object($object)) {
            $object = (array) $object;
        }

        if (is_array($object)) {
            foreach ($object as $key => $value) {
                $value = $value instanceof SimpleXMLElement ? self::normalize($value) : $value;

                if ('@attributes' === $key) {
                    $result = $value; // @codeCoverageIgnore
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param  array<int|string,mixed>  $data
     * @param  string  $item
     * @param  string  $id
     *
     * @return string
     */
    protected static function data2Xml(array $data, string $item = 'item', string $id = 'id'): string
    {
        $xml = $attr = '';

        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }

            $xml .= "<{$key}{$attr}>";

            if ((is_array($val) || is_object($val))) {
                $xml .= self::data2Xml((array) $val, $item, $id);
            } else {
                /** @phpstan-ignore-next-line */
                $xml .= is_numeric($val) ? $val : self::cdata($val);
            }

            $xml .= "</{$key}>";
        }

        return $xml;
    }

    /**
     * Delete invalid characters in XML.
     *
     * @see https://www.w3.org/TR/2008/REC-xml-20081126/#charsets - XML charset range
     * @see http://php.net/manual/en/regexp.reference.escape.php - escape in UTF-8 mode
     *
     * @param  ?string  $xml
     *
     * @return string
     */
    public static function sanitize(?string $xml): string
    {
        if (empty($xml)) {
            return '';
        }

        return preg_replace(
            '/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]+/u',
            '',
            $xml
        ) ?? '';
    }
}
