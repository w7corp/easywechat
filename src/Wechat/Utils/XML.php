<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * XML.php.
 *
 * Part of Overtrue\Wechat.
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

namespace Overtrue\Wechat\Utils;

/**
 * XML 工具类，用于构建与解析 XML.
 */
class XML
{
    /**
     * XML 转换为数组.
     *
     * @param string $xml XML string
     *
     * @return array
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
     * XML编码
     *
     * @param mixed  $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
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
            $_attr = array();

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
     * 生成<![CDATA[%s]]>.
     *
     * @param string $string 内容
     *
     * @return string
     */
    public static function cdata($string)
    {
        return sprintf('<![CDATA[%s]]>', $string);
    }

    /**
     * 把对象转换成数组.
     *
     * @param string $data 数据
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
     * 转换数组为xml.
     *
     * @param array  $data 数组
     * @param string $item item的属性名
     * @param string $id   id的属性名
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
}
