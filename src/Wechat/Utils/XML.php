<?php

namespace Overtrue\Wechat\Utils;

class XML {


    /**
     * XML 转换为数组
     *
     * @param string        $xml
     *
     * @return array
     */
    static public function parse($xml)
    {
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);

        if (is_object($data) && get_class($data) == 'SimpleXMLElement') {
            $data = self::arrarval($data);
        }

        return $data;
    }

    /**
     * XML编码
     *
     * @param mixed  $data     数据
     * @param string $root     根节点名
     * @param string $item     数字索引的子节点名
     * @param string $attr     根节点属性
     * @param string $id       数字索引子节点key转换的属性名
     *
     * @return string
     */
    static public function build($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id' /*, $encoding = 'utf-8'*/)
    {
        if (is_array($attr)) {
            $_attr = array();

            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }

            $attr = implode(' ', $_attr);
        }

        $attr   = trim($attr);
        $attr   = empty($attr) ? '' : " {$attr}";
        /*$xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";*/
        $xml   = "<{$root}{$attr}>";
        $xml   .= self::data2Xml($data, $item, $id);
        $xml   .= "</{$root}>";

        return $xml;
    }

    /**
     * 生成<![CDATA[%s]]>
     *
     * @param string $string
     *
     * @return string
     */
    static public function cdata($string)
    {
        return sprintf('<![CDATA[%s]]>', $string);
    }

    /**
     * 把对象转换成数组
     *
     * @param string $data
     *
     * @return array
     */
    static private function arrarval($data)
    {
        if (is_object($data) && get_class($data) == 'SimpleXMLElement') {
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
     * 转换数组为xml
     *
     * @param array  $data
     * @param string $item
     * @param string $id
     *
     * @return string
     */
    static private function data2Xml($data, $item = 'item', $id = 'id')
    {
        $xml = $attr = '';

        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key  = $item;
            }

            $xml .=  "<{$key}{$attr}>";
            $xml .=  (is_array($val) || is_object($val))
                    ? self::data2Xml((array) $val, $item, $id) : (is_numeric($val) ? $val : self::cdata($val));
            $xml .=  "</{$key}>";
        }

        return $xml;
    }
}