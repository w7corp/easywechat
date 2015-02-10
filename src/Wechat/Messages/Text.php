<?php

namespace Overtrue\Wechat\Messages;

/**
 * @property string $content
 */
class Text extends BaseMessage
{

    protected $properties = array('content');

    /**
     * 生成主动消息数组
     */
    public function toStaff()
    {
        return array(
                'text' => array(
                           'content' => $this->content,
                          ),
        );
    }

    /**
     * 生成回复消息数组
     */
    public function toReply()
    {
        return array(
                     'Content' => $this->content,
                    );
    }

}