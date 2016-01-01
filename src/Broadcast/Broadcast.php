<?php

/*
 * This file is part of the EasyWeChat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Broadcast.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Broadcast;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\Exceptions\HttpException;

/**
 * Class Broadcast.
 */
class Broadcast extends AbstractAPI
{
    const API_SEND_BY_GROUP = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall';
    const API_SEND_BY_OPENID = 'https://api.weixin.qq.com/cgi-bin/message/mass/send';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete';
    const API_PREVIEW = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview';
    const API_GET = 'http://api.weixin.qq.com/cgi-bin/message/mass/get';

    const PREVIEW_BY_OPENID = 'touser';
    const PREVIEW_BY_WXH = 'towxname';

    const MSG_TYPE_TEXT = 'text'; // 文本
    const MSG_TYPE_NEWS = 'news'; // 图文
    const MSG_TYPE_VOICE = 'voice'; // 语音
    const MSG_TYPE_IMAGE = 'image'; // 图片
    const MSG_TYPE_VIDEO = 'video'; // 视频
    const MSG_TYPE_CARD = 'card'; // 卡券

    /**
     * Send a message.
     *
     * @param string $msgType message type
     * @param mixed $message message
     * @param mixed $to
     *
     * @return mixed
     */
    public function send($msgType, $message, $to = null)
    {
        $message = (new MessageBuilder())->msgType($msgType)->message($message)->to($to)->build();

        $api = is_array($to) ? self::API_SEND_BY_OPENID : self::API_SEND_BY_GROUP;

        return $this->post($api, $message);
    }

    /**
     * Preview a message.
     *
     * @param string $msgType message type
     * @param mixed $message message
     * @param string $to
     * @param string $by
     *
     * @return mixed
     */
    public function preview($msgType, $message, $to, $by = self::PREVIEW_BY_OPENID)
    {
        $message = (new MessageBuilder())->msgType($msgType)->message($message)->to($to)->buildPreview($by);

        return $this->post(self::API_PREVIEW, $message);
    }

    /**
     * Delete a broadcast.
     *
     * @param string $msgId
     *
     * @return bool
     */
    public function delete($msgId)
    {
        $options = [
            'msg_id' => $msgId,
        ];

        return $this->post(self::API_DELETE, $options);
    }

    /**
     * Get a broadcast status.
     *
     * @param string $msgId
     *
     * @return array
     */
    public function status($msgId)
    {
        $options = [
            'msg_id' => $msgId,
        ];

        return $this->post(self::API_GET, $options);
    }

    /**
     * post request.
     *
     * @param string $url
     * @param array|string $options
     *
     * @return array|bool
     *
     * @throws HttpException
     */
    private function post($url, $options)
    {
        return $this->parseJSON('post', [$url, $options]);
    }
}
