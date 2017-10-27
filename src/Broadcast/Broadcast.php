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
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
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
    const API_GET = 'https://api.weixin.qq.com/cgi-bin/message/mass/get';

    const PREVIEW_BY_OPENID = 'touser';
    const PREVIEW_BY_NAME = 'towxname';

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
     * @param mixed  $message message
     * @param mixed  $to
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
     * Send a text message.
     *
     * @param mixed $message message
     * @param mixed $to
     *
     * @return mixed
     */
    public function sendText($message, $to = null)
    {
        return $this->send(self::MSG_TYPE_TEXT, $message, $to);
    }

    /**
     * Send a news message.
     *
     * @param mixed $message message
     * @param mixed $to
     *
     * @return mixed
     */
    public function sendNews($message, $to = null)
    {
        return $this->send(self::MSG_TYPE_NEWS, $message, $to);
    }

    /**
     * Send a voice message.
     *
     * @param mixed $message message
     * @param mixed $to
     *
     * @return mixed
     */
    public function sendVoice($message, $to = null)
    {
        return $this->send(self::MSG_TYPE_VOICE, $message, $to);
    }

    /**
     * Send a image message.
     *
     * @param mixed $message message
     * @param mixed $to
     *
     * @return mixed
     */
    public function sendImage($message, $to = null)
    {
        return $this->send(self::MSG_TYPE_IMAGE, $message, $to);
    }

    /**
     * Send a video message.
     *
     * @param mixed $message message
     * @param mixed $to
     *
     * @return mixed
     */
    public function sendVideo($message, $to = null)
    {
        return $this->send(self::MSG_TYPE_VIDEO, $message, $to);
    }

    /**
     * Send a card message.
     *
     * @param mixed $message message
     * @param mixed $to
     *
     * @return mixed
     */
    public function sendCard($message, $to = null)
    {
        return $this->send(self::MSG_TYPE_CARD, $message, $to);
    }

    /**
     * Preview a message.
     *
     * @param string $msgType message type
     * @param mixed  $message message
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
     * Preview a text message.
     *
     * @param mixed  $message message
     * @param string $to
     * @param string $by
     *
     * @return mixed
     */
    public function previewText($message, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->preview(self::MSG_TYPE_TEXT, $message, $to, $by);
    }

    /**
     * Preview a news message.
     *
     * @param mixed  $message message
     * @param string $to
     * @param string $by
     *
     * @return mixed
     */
    public function previewNews($message, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->preview(self::MSG_TYPE_NEWS, $message, $to, $by);
    }

    /**
     * Preview a voice message.
     *
     * @param mixed  $message message
     * @param string $to
     * @param string $by
     *
     * @return mixed
     */
    public function previewVoice($message, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->preview(self::MSG_TYPE_VOICE, $message, $to, $by);
    }

    /**
     * Preview a image message.
     *
     * @param mixed  $message message
     * @param string $to
     * @param string $by
     *
     * @return mixed
     */
    public function previewImage($message, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->preview(self::MSG_TYPE_IMAGE, $message, $to, $by);
    }

    /**
     * Preview a video message.
     *
     * @param mixed  $message message
     * @param string $to
     * @param string $by
     *
     * @return mixed
     */
    public function previewVideo($message, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->preview(self::MSG_TYPE_VIDEO, $message, $to, $by);
    }

    /**
     * Preview a card message.
     *
     * @param mixed  $message message
     * @param string $to
     * @param string $by
     *
     * @return mixed
     */
    public function previewCard($message, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->preview(self::MSG_TYPE_CARD, $message, $to, $by);
    }

    /**
     * Preview a message by name.
     *
     * @param string $msgType message type
     * @param mixed  $message message
     * @param $to
     *
     * @return mixed
     */
    public function previewByName($msgType, $message, $to)
    {
        return $this->preview($msgType, $message, $to, self::PREVIEW_BY_NAME);
    }

    /**
     * Preview a text message by name.
     *
     * @param mixed $message message
     * @param $to
     *
     * @return mixed
     */
    public function previewTextByName($message, $to)
    {
        return $this->preview(self::MSG_TYPE_TEXT, $message, $to, self::PREVIEW_BY_NAME);
    }

    /**
     * Preview a news message by name.
     *
     * @param mixed $message message
     * @param $to
     *
     * @return mixed
     */
    public function previewNewsByName($message, $to)
    {
        return $this->preview(self::MSG_TYPE_NEWS, $message, $to, self::PREVIEW_BY_NAME);
    }

    /**
     * Preview a voice message by name.
     *
     * @param mixed $message message
     * @param $to
     *
     * @return mixed
     */
    public function previewVoiceByName($message, $to)
    {
        return $this->preview(self::MSG_TYPE_VOICE, $message, $to, self::PREVIEW_BY_NAME);
    }

    /**
     * Preview a image message by name.
     *
     * @param mixed $message message
     * @param $to
     *
     * @return mixed
     */
    public function previewImageByName($message, $to)
    {
        return $this->preview(self::MSG_TYPE_IMAGE, $message, $to, self::PREVIEW_BY_NAME);
    }

    /**
     * Preview a video message by name.
     *
     * @param mixed $message message
     * @param $to
     *
     * @return mixed
     */
    public function previewVideoByName($message, $to)
    {
        return $this->preview(self::MSG_TYPE_VIDEO, $message, $to, self::PREVIEW_BY_NAME);
    }

    /**
     * Preview a card message by name.
     *
     * @param mixed $message message
     * @param $to
     *
     * @return mixed
     */
    public function previewCardByName($message, $to)
    {
        return $this->preview(self::MSG_TYPE_CARD, $message, $to, self::PREVIEW_BY_NAME);
    }

    /**
     * Delete a broadcast.
     *
     * @param string $msgId
     *
     * @return \EasyWeChat\Support\Collection
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
     * @return \EasyWeChat\Support\Collection
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
     * @param string       $url
     * @param array|string $options
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws HttpException
     */
    private function post($url, $options)
    {
        return $this->parseJSON('json', [$url, $options]);
    }
}
