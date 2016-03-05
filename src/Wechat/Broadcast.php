<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Broadcast.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Messages\BaseMessage;

class Broadcast
{
    const API_SEND_BY_GROUP = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall';
    const API_SEND_BY_OPENID = 'https://api.weixin.qq.com/cgi-bin/message/mass/send';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete';
    const API_PREVIEW = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview';
    const API_GET = 'http://api.weixin.qq.com/cgi-bin/message/mass/get';

    const PREVIEW_BY_OPENID = 'touser';
    const PREVIEW_BY_WXH = 'towxname';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * 消息.
     *
     * @var \Overtrue\Wechat\Messages\BaseMessage;
     */
    protected $message;

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 准备消息.
     *
     * @param \Overtrue\Wechat\Messages\BaseMessage $message
     *
     * @return Broadcast
     */
    public function send($message)
    {
        is_string($message) && $message = Message::make('text')->with('content', $message);

        if (!$message instanceof BaseMessage) {
            throw new \Exception("消息必须继承自 'Overtrue\\Wechat\\BaseMessage'");
        }

        $this->message = $message;

        return $this;
    }

    /**
     * 发送消息.
     *
     * @param string $group 组或oppenid
     *
     * @return bool
     */
    public function to($group = null)
    {
        if (empty($this->message)) {
            throw new Exception('未设置要发送的消息');
        }

        $this->message->to_group = $group;

        $apiSend = is_array($group) ? self::API_SEND_BY_OPENID : self::API_SEND_BY_GROUP;

        return $this->http->jsonPost($apiSend, $this->message->buildForBroadcast());
    }

    /**
     * 删除群发.
     *
     * @param string $msgId 发出去的消息ID
     *
     * @return bool
     */
    public function delete($msgId)
    {
        return $this->http->jsonPost(self::API_DELETE, array('msg_id' => $msgId));
    }

    /**
     * 预览.
     *
     * @param string $openId 接收消息用户对应该公众号的openid
     * @param string $type   接收消息用户的类型
     *
     * @return bool
     */
    public function preview($openId, $type = self::PREVIEW_BY_OPENID)
    {
        $this->message->to = $openId;

        return $this->http->jsonPost(self::API_PREVIEW, $this->message->buildForBroadcastPreview($type));
    }

    /**
     * 查询群发消息发送状态
     *
     * @param string $msgId 全消息ID
     *
     * @return array
     */
    public function status($msgId)
    {
        return $this->http->jsonPost(self::API_GET, array('msg_id' => $msgId));
    }
}
