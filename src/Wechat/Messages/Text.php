<?php namespace Overtrue\Wechat\Messages;


class Text extends AbstractMessage implements MessageInterface {

    protected $properties = array('content');

    /**
     * 设置内容
     *
     * @param string $content
     *
     * @return Overtrue\Wechat\Messages\Text
     */
    public function content($content)
    {
        $this->attributes['content'] = $content;

        return $this;
    }

    public function formatToClient()
    {
        return array(
                "touser"  => $this->to,
                "msgtype" => "text",
                "text"    => array(
                                 "content" => $this->content
                            ),
        );
    }

    public function formatToServer()
    {
        $format = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>';

        return sprintf($format, $this->to, $this->from, time(), $this->content);
    }

}