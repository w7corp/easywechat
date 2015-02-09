<?php

namespace Overtrue\Wechat\Messages;

use Exception;
use Overtrue\Wechat\Utils\XML;

/**
 * @property string $content
 */
class Link extends AbstractMessage
{

    protected $properties = array('title', 'description', 'url');

    public function formatToClient()
    {
        throw new Exception("暂时不支持发送链接消息");
    }

    public function formatToServer()
    {
        throw new Exception("暂时不支持回复链接消息");
    }

}