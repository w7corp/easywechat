<?php

namespace Overtrue\Wechat\Services;

use Closure;
use InvalidArgumentException;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Messages\BaseMessage;

class Message extends Service
{
    /**
     * 消息类型
     */
    const TEXT      = 'text';
    const IMAGE     = 'image';
    const VOICE     = 'voice';
    const VIDEO     = 'video';
    const MUSIC     = 'music';
    const NEWS      = 'news';
    const TRANSFER  = 'transfer';
    const NEWS_ITEM = 'news_item';


    /**
     * 创建消息实例
     */
    public function make($type = self::TEXT)
    {
        if (!defined(__CLASS__ . '::' . strtoupper($type))) {
            throw new InvalidArgumentException("Error Message Type '{$type}'");
        }

        $message = "Overtrue\Wechat\Messages\\"
                    . str_replace(' ', '', ucwords(str_replace(array('-', '_'), ' ', $type)));

        return new $message;
    }
}