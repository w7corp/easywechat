<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Exception;
/**
 * @property string $content
 */
class Link extends BaseMessage
{

    protected $properties = array('title', 'description', 'url');
}