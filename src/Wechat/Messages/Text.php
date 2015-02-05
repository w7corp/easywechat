<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Utils\XML;

class Text extends AbstractMessage implements MessageInterface
{

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
                'touser'  => $this->to,
                'msgtype' => 'text',
                'text'    => array(
                              'content' => $this->content,
                             ),
        );
    }

    public function formatToServer()
    {
        $response = array(
                     'ToUserName'   => $this->to,
                     'FromUserName' => $this->from,
                     'CreateTime'   => time(),
                     'MsgType'      => 'text',
                     'Content'      => $this->content,
                    );

        return XML::build($response);
    }

}