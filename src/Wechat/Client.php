<?php namespace Overtrue\Wechat;

use Exception;
use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Utils\Http;
use Overtrue\Wechat\Messages\MessageInterface;

class Client {

    /**
     * 设置
     *
     * @var Overtrue\Wechat\Utils\Bag
     */
    protected $options;


    /**
     * 初始化参数
     *
     * @param array $options
     *
     * @return mixed
     */
    public function __construct($options)
    {
        $this->options = new Bag($options);
    }

    /**
     * 发送消息
     *
     * @param Message $message
     *
     * @return $this
     */
    public function send(MessageInterface $message)
    {
        $url = Wechat::makeUrl('message.send');
        error_log($url);
error_log(json_encode($message->formatToClient()));
        return Wechat::post($url, $message->formatToClient());
    }
}