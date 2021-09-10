<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Support;

use SimpleXMLElement;

class Xml
{
    public static function parse(?string $xml): array | null
    {
        if (empty($xml)) {
            return null;
        }

        return self::normalize(
            simplexml_load_string(self::sanitize($xml), 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NOBLANKS)
        );
    }

    public static function build(
        array $data,
        string $root = 'xml',
        string $item = 'item',
        string | array $attr = '',
        string $id = 'id'
    ): string {
        if (is_array($attr)) {
            $segments = [];

            foreach ($attr as $key => $value) {
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
        return sprintf('<![CDATA[%s]]>', $string);
    }

    protected static function normalize(SimpleXMLElement $object): array | null
    {
        $result = null;

        if (\is_object($object)) {
            $object = (array)$object;
        }

        if (is_array($object)) {
            foreach ($object as $key => $value) {
                $value = \is_a($value, SimpleXMLElement::class) ? self::normalize($value) : $value;

                if ('@attributes' === $key) {
                    $result = $value; // @codeCoverageIgnore
                } else {
                    $result[$key] = $value;
                }
            }
        } else {
            $result = $object;
        }

        return $result;
    }

    protected static function data2Xml(array $data, string $item = 'item', string $id = 'id'): string
    {
        $xml = $attr = '';

        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }

            $xml .= "<{$key}{$attr}>";

            if ((is_array($val) || is_object($val))) {
                $xml .= self::data2Xml((array)$val, $item, $id);
            } else {
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
     * @param  ?string $xml
     *
     * @return string|null
     */
    public static function sanitize(?string $xml): string | null
    {
        if (empty($xml)) {
            return $xml;
        }

        return preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]+/u', '', $xml);
    }
}
