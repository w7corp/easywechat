<?php

namespace Overtrue\Wechat\Messages;

use Exception;

/**
 * @property string $content
 */
class Link extends BaseMessage
{

    protected $properties = array('title', 'description', 'url');
}