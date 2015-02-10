<?php

namespace Overtrue\Wechat\Messages;

class NewsItem extends BaseMessage
{
    protected $properties = array('title', 'description', 'pic_url', 'url');
}