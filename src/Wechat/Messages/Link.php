<?php

namespace Overtrue\Wechat\Messages;

/**
 * 链接消息
 *
 * @property string $content
 */
class Link extends BaseMessage
{
    protected $properties = array('title', 'description', 'url');
}
