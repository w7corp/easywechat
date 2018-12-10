<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Support;

use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Tests\TestCase;

class XMLTest extends TestCase
{
    public function testParse()
    {
        $xml = '<xml>
            <foo>name</foo>
            <bar>age</bar>
            <name><![CDATA[text here]]></name>
        </xml>';

        $this->assertSame(['foo' => 'name', 'bar' => 'age', 'name' => 'text here'], XML::parse($xml));

        $xml = '<xml>'
            .'<id color="blue"><![CDATA[bk101]]></id>'
            .'<author><![CDATA[Gambardella, Matthew]]></author>'
            .'<title><![CDATA[XML Developer\'s Guide]]></title>'
            .'<genre><![CDATA[Computer]]></genre>'
            .'<price>44.95</price>'
            .'<publish_date><![CDATA[2000-10-01]]></publish_date>'
            .'<description><![CDATA[An in-depth look at creating applications with XML.]]></description>'
            .'</xml>';
        $array = XML::parse($xml);
        $this->assertSame([
            'id' => 'bk101',
            'author' => 'Gambardella, Matthew',
            'title' => 'XML Developer\'s Guide',
            'genre' => 'Computer',
            'price' => '44.95',
            'publish_date' => '2000-10-01',
            'description' => 'An in-depth look at creating applications with XML.',
        ], $array);
    }

    public function testBuild()
    {
        $data = [
            'id' => 'bk101',
            'author' => 'Gambardella, Matthew',
            'title' => 'XML Developer\'s Guide',
            'genre' => 'Computer',
            'price' => '44.95',
            'items' => ['foo', 'bar'],
            'publish_date' => '2000-10-01',
            'description' => 'An in-depth look at creating applications with XML.',
        ];

        $this->assertSame('<xml version="1.0">'
                        .'<id><![CDATA[bk101]]></id>'
                        .'<author><![CDATA[Gambardella, Matthew]]></author>'
                        .'<title><![CDATA[XML Developer\'s Guide]]></title>'
                        .'<genre><![CDATA[Computer]]></genre>'
                        .'<price>44.95</price>'
                        .'<items><item id="0"><![CDATA[foo]]></item>'
                        .'<item id="1"><![CDATA[bar]]></item></items>'
                        .'<publish_date><![CDATA[2000-10-01]]></publish_date>'
                        .'<description><![CDATA[An in-depth look at creating applications with XML.]]></description>'
                        .'</xml>', XML::build($data, 'xml', 'item', ['version' => '1.0']));
    }

    public function testCdata()
    {
        $this->assertSame('<![CDATA[text here]]>', XML::cdata('text here'));
    }

    public function testSanitize()
    {
        $content_template = '<1%s%s%s234%s微信测试%sabcd?*_^%s@#%s%s%s>';
        $valid_chars = preg_replace('/(%s)+/', '', $content_template);
        $invalid_chars = sprintf($content_template, "\x1", "\x02", "\3", "\u{05}", "\xe", "\xF", "\u{00FFFF}", "\xC", "\10");

        $xml_template = '<xml><foo>We shall filter out invalid chars</foo><bar><![CDATA[%s]]></bar></xml>';

        $element = 'SimpleXMLElement';
        $option = LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NOBLANKS;

        $invalid_xml = sprintf($xml_template, $invalid_chars);
        libxml_use_internal_errors(true);
        $this->assertFalse(simplexml_load_string($invalid_xml, $element, $option));
        libxml_use_internal_errors(false);

        $valid_xml = sprintf($xml_template, $valid_chars);

        $this->assertSame(
            (array) simplexml_load_string($valid_xml, $element, $option),
            (array) simplexml_load_string(XML::sanitize($invalid_xml), $element, $option)
        );
    }
}
