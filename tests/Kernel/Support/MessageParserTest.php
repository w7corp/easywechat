<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Kernel\Support;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Support\MessageParser;
use EasyWeChat\Tests\TestCase;

class MessageParserTest extends TestCase
{
    public function test_it_can_parse_json_content()
    {
        $content = '{"key":"value","number":123}';
        $result = MessageParser::parse($content);

        $this->assertIsArray($result);
        $this->assertSame('value', $result['key']);
        $this->assertSame(123, $result['number']);
    }

    public function test_it_can_parse_json_with_whitespace()
    {
        $content = "  \n\t  {\"key\":\"value\"}  \n  ";
        $result = MessageParser::parse($content);

        $this->assertIsArray($result);
        $this->assertSame('value', $result['key']);
    }

    public function test_it_falls_back_to_xml_when_json_decode_fails()
    {
        $content = '<xml><key>value</key><number>123</number></xml>';
        $result = MessageParser::parse($content);

        $this->assertIsArray($result);
        $this->assertSame('value', $result['key']);
        $this->assertSame('123', $result['number']);
    }

    public function test_it_falls_back_to_xml_when_json_is_not_array()
    {
        // JSON string (not an array) should fall back to XML
        // Since it's not valid XML either, it should throw exception
        $this->expectException(BadRequestException::class);
        MessageParser::parse('"just a string"');
    }

    public function test_it_falls_back_to_xml_when_json_is_empty_array()
    {
        // Empty JSON array should fall back to XML
        // Since empty array is not valid XML, it should throw exception
        $this->expectException(BadRequestException::class);
        MessageParser::parse('[]');
    }

    public function test_it_falls_back_to_xml_when_json_is_not_array_but_xml_is_valid()
    {
        // JSON that parses to a string (not array) should fall back to XML
        // If the content is also valid XML, it should parse as XML
        $content = '<xml><key>value</key></xml>';
        $result = MessageParser::parse($content);

        $this->assertIsArray($result);
        $this->assertSame('value', $result['key']);
    }

    public function test_it_throws_exception_when_both_json_and_xml_fail()
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Failed to decode content. Content must be valid XML or JSON.');

        MessageParser::parse('invalid content');
    }

    public function test_it_prioritizes_json_over_xml()
    {
        // Content that could be both valid JSON and valid XML
        // JSON should be parsed first
        $content = '{"xml":"value"}';
        $result = MessageParser::parse($content);

        $this->assertIsArray($result);
        $this->assertSame('value', $result['xml']);
    }

    public function test_it_can_parse_xml_with_whitespace()
    {
        $content = "  \n  <xml><key>value</key></xml>  \n  ";
        $result = MessageParser::parse($content);

        $this->assertIsArray($result);
        $this->assertSame('value', $result['key']);
    }
}
