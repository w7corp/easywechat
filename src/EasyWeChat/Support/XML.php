<?php

/**
 * XML.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Support;

/**
 * Class XML.
 */
class XML
{
    /**
     * XML to array.
     *
     * @param string $xml XML string
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public static function parse($xml)
    {
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);

        if (is_object($data) && get_class($data) === 'SimpleXMLElement') {
            $data = self::arrarval($data);
        }

        return $data;
    }

    /**
     * XML encode.
     *
     * @param mixed  $data
     * @param string $root
     * @param string $item
     * @param string $attr
     * @param string $id
     *
     * @return string
     */
    public static function build(
        $data,
        $root = 'xml',
        $item = 'item',
        $attr = '',
        $id = 'id'
    ) {
        if (is_array($attr)) {
            $_attr = [];

            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }

            $attr = implode(' ', $_attr);
        }

        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml  .= self::data2Xml($data, $item, $id);
        $xml  .= "</{$root}>";

        return $xml;
    }

    /**
     * Build CDATA.
     *
     * @param string $string
     *
     * @return string
     */
    public static function cdata($string)
    {
        return sprintf('<![CDATA[%s]]>', $string);
    }

    /**
     * Object to array.
     *
     * @param string $data
     *
     * @return array
     */
    private static function arrarval($data)
    {
        if (is_object($data) && get_class($data) === 'SimpleXMLElement') {
            $data = (array) $data;
        }

        if (is_array($data)) {
            foreach ($data as $index => $value) {
                $data[$index] = self::arrarval($value);
            }
        }

        return $data;
    }

    /**
     * Array to XML.
     *
     * @param array  $data
     * @param string $item
     * @param string $id
     *
     * @return string
     */
    private static function data2Xml($data, $item = 'item', $id = 'id')
    {
        $xml = $attr = '';

        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }

            $xml .= "<{$key}{$attr}>";

            if ((is_array($val) || is_object($val))) {
                $xml .= self::data2Xml((array) $val, $item, $id);
            } else {
                $xml .= is_numeric($val) ? $val : self::cdata($val);
            }

            $xml .= "</{$key}>";
        }

        return $xml;
    }
}//end class

