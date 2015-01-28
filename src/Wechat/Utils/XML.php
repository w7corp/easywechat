<?php namespace Overtrue\Wechat\Utils;

class XML {

    /**
     * XML 转换为数组
     *
     * @param string        $xml
     * @param callback|null $callback
     *
     * @return array
     */
    static public function parse($xml, callback $callback = null)
    {
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);

        if (is_object($data) && get_class($data) == 'SimpleXMLElement') {
            $data = (array) $data;

            array_walk_recursive($data, function($item)
            {
                if (is_object($item) && get_class($item) != 'SimpleXMLElement') {
                    $item = (array) $item;
                }
            });
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
     * @param string $encoding 数据编码
     *
     * @return string
     */
    static public function build($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8')
    {
        if (is_array($attr)) {
            $_attr = array();

            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }

            $attr = implode(' ', $_attr);
        }

        function data2Xml($data, $item = 'item', $id = 'id')
        {
            $xml = $attr = '';

            foreach ($data as $key => $val) {
                if (is_numeric($key)) {
                    $id && $attr = " {$id}=\"{$key}\"";
                    $key  = $item;
                }
                $xml .=  "<{$key}{$attr}>";
                $xml .=  (is_array($val) || is_object($val)) ? data2Xml($val, $item, $id) : $val;
                $xml .=  "</{$key}>";
            }

            return $xml;
        }

        $attr   = trim($attr);
        $attr   = empty($attr) ? '' : " {$attr}";
        $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml   .= "<{$root}{$attr}>";
        $xml   .= data2Xml($data, $item, $id);
        $xml   .= "</{$root}>";

        return $xml;
    }
}