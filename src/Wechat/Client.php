<?php namespace Overtrue\Wechat;

use Exception;
use Overtrue\Wechat\Utils\Bag;
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
     * 获取用户信息
     *
     * @param string $openId
     * @param string $lang
     *
     * @return Overtrue\Wechat\Utils\Bag
     */
    public function user($openId, $lang = 'zh_CN')
    {
        return new User($openId, $lang);
    }

    /**
     * 获取用户列表
     *
     * @param string $nextOpenId
     *
     * @return Overtrue\Wechat\Utils\Bag
     */
    public function users($nextOpenId = null)
    {
        return new Bag(Wechat::get(Wechat::makeUrl('user.list', array('next_openid' => $nextOpenId))));
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

        return Wechat::post($url, $message->formatToClient());
    }
}