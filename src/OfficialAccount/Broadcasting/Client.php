<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Broadcasting;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function send(string $msgType, $message, $to = null)
    {
        $message = (new MessageBuilder())->msgType($msgType)->message($message)->to($to)->build();

        $api = is_array($to) ? 'cgi-bin/message/mass/send' : 'cgi-bin/message/mass/sendall';

        return $this->httpPostJson($api, $message);
    }

    /**
     * Send a text message.
     *
     * @param mixed $message message
     * @param mixed $to
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function preview($msgType, $message, $to, $by = self::PREVIEW_BY_OPENID)
    {
        $message = (new MessageBuilder())->msgType($msgType)->message($message)->to($to)->buildPreview($by);

        return $this->httpPostJson('cgi-bin/message/mass/preview', $message);
    }

    /**
     * Preview a text message.
     *
     * @param mixed  $message message
     * @param string $to
     * @param string $by
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete($msgId)
    {
        $options = [
            'msg_id' => $msgId,
        ];

        return $this->httpPostJson('cgi-bin/message/mass/delete', $options);
    }

    /**
     * Get a broadcast status.
     *
     * @param string $msgId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function status($msgId)
    {
        $options = [
            'msg_id' => $msgId,
        ];

        return $this->httpPostJson('cgi-bin/message/mass/get', $options);
    }
}
