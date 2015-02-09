<?php

namespace Overtrue\Wechat\Messages;

use Exception;
use Overtrue\Wechat\Utils\XML;

/**
 * @property string $content
 */
class Location extends AbstractMessage
{

    protected $properties = array('lat', 'lon', 'scale', 'label');

    /**
     * @see OvertrueWechatMessagesAbstractMessage::buildForStaff();
     */
    public function buildForStaff)
    {
        throw new Exception("暂时不支持发送链接消息");
    }

    /**
     * @see OvertrueWechatMessagesAbstractMessage::buildForReply();
     */
    public function buildForReply)
    {
        throw new Exception("暂时不支持回复链接消息");
    }
}