<?php
namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Exception;

/**
 * 链接消息
 *
 * @property string $content
 */
class Link extends BaseMessage
{

    protected $properties = array('title', 'description', 'url');
}