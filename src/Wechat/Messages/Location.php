<?php

namespace Overtrue\Wechat\Messages;

use Exception;

/**
 * @property string $content
 */
class Location extends BaseMessage
{

    protected $properties = array('lat', 'lon', 'scale', 'label');

    /**
     * 生成主动消息数组
     */
    public function toStaff()
    {
        throw new Exception("暂时不支持发送链接消息");
    }

    /**
     * 生成回复消息数组
     */
    public function toReply()
    {
        throw new Exception("暂时不支持回复链接消息");
    }
}