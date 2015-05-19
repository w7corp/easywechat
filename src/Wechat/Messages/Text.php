<?php
/**
 * Part of Overtrue\Wechat
 *
 * @author overtrue <i@overtrue.me>
 */

namespace Overtrue\Wechat\Messages;

/**
 * 文本消息
 *
 * @property string $content
 */
class Text extends BaseMessage
{

    /**
     * 属性
     *
     * @var array
     */
    protected $properties = array('content');

    /**
     * 生成主动消息数组
     *
     * @return array
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
     *
     * @return array
     */
    public function toReply()
    {
        return array(
                'Content' => $this->content,
               );
    }
}
