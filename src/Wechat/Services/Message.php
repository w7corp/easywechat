<?php

namespace Overtrue\Wechat\Services;

use Closure;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Messages\AbstractMessage;
use InvalidArgumentException;

class Message extends Service
{

    const API_SEND = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';

    /**
     * 消息类型
     */
    const TEXT  = 'text';
    const IMAGE = 'image';
    const VOICE = 'voice';
    const VIDEO = 'video';
    const MUSIC = 'music';
    const NEWS  = 'news';


    /**
     * 创建消息实例
     *
     * @param string $type
     *
     * @return Overtrue\Wechat\Message
     */
    static public function make($type)
    {
        if (!defined(__CLASS__ . '::' . strtoupper($type))) {
            throw new InvalidArgumentException("Error Message Type '{$type}'");
        }

        $message = "Overtrue\Wechat\Messages\\" . ucfirst(strtolower($type));

        return new $message;
    }

    public function send()
    {
        # code...
    }
}