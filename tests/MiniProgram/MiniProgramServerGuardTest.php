<?php

use EasyWeChat\MiniProgram\Server\Guard;
use EasyWeChat\Support\XML;
use Symfony\Component\HttpFoundation\Request;

class MiniProgramServerGuardTest extends TestCase
{
    public function getServer($queries = [], $content = null)
    {
        $queries = array_merge([
            'signature' => 'b4d6a65981dd51a8d6d022364500e4430fe5e577',
            'timestamp' => '1490925589',
            'nonce' => '1854495049',
        ], $queries);

        return new Guard('Mi5u8if', new Request($queries, [], [], [], [], [], $content));
    }

    public function testValidateRequest()
    {
        $result = $this->getServer(['echostr' => '3804283725124844375'])->serve();

        $this->assertEquals('3804283725124844375', $result->getContent());
    }

    public function testUserEnterSession()
    {
        $json = json_encode([
            'ToUserName' => 'toUser',
            'FromUserName' => 'fromUser',
            'CreateTime' => 1482048670,
            'MsgType' => 'event',
            'Event' => 'user_enter_tempsession',
            'SessionFrom' => 'sessionFrom',
        ]);
        $xml = '<xml>
                    <ToUserName><![CDATA[toUser]]></ToUserName>
                    <FromUserName><![CDATA[fromUser]]></FromUserName>
                    <CreateTime>1482048670</CreateTime>
                    <MsgType><![CDATA[event]]></MsgType>
                    <Event><![CDATA[user_enter_tempsession]]></Event>
                    <SessionFrom><![CDATA[sessionFrom]]></SessionFrom>
                </xml>';

        $server = $this->getServer([], $json);

        $res = $server->setMessageHandler(function ($message) {
            $this->assertEquals('user_enter_tempsession', $message->Event);
        })->serve();
        $this->assertEquals('success', $res->getContent());

        $server = $this->getServer([], $xml);

        $res2 = $server->setMessageHandler(function ($message) {
            $this->assertEquals('user_enter_tempsession', $message->Event);
        })->serve();
        $this->assertEquals('success', $res2->getContent());
    }
}
