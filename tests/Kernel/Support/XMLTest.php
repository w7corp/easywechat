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
}
