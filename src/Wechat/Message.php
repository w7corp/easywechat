<?php namespace Overtrue\Wechat;

use Closure;
use InvalidArgumentException;

class Message {

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
}