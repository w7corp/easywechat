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
use EasyWeChat\Kernel\Contracts\MessageInterface;
use EasyWeChat\Kernel\Messages\Card;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Media;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Support\Arr;

/**
 * Class Client.
 *
 * @method \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string previewTextByName($text, $wxname);
 * @method \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string previewNewsByName($mediaId, $wxname);
 * @method \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string previewVoiceByName($mediaId, $wxname);
 * @method \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string previewImageByName($mediaId, $wxname);
 * @method \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string previewVideoByName($message, $wxname);
 * @method \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string previewCardByName($cardId, $wxname);
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    const PREVIEW_BY_OPENID = 'touser';
    const PREVIEW_BY_NAME = 'towxname';

    /**
     * Send a message.
     *
     * @param array $message
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function send(array $message)
    {
        $api = Arr::get($message, 'filter.is_to_all') ? 'cgi-bin/message/mass/sendall' : 'cgi-bin/message/mass/send';

        return $this->httpPostJson($api, $message);
    }

    /**
     * Preview a message.
     *
     * @param array $message
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function preview(array $message)
    {
        return $this->httpPostJson('cgi-bin/message/mass/preview', $message);
    }

    /**
     * Delete a broadcast.
     *
     * @param string $msgId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete(string $msgId)
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
    public function status(string $msgId)
    {
        $options = [
            'msg_id' => $msgId,
        ];

        return $this->httpPostJson('cgi-bin/message/mass/get', $options);
    }

    /**
     * Send a text message.
     *
     * @param string $message
     * @param mixed  $to
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function sendText(string $message, $to = null)
    {
        return $this->sendMessage(new Text($message), $to);
    }

    /**
     * Send a news message.
     *
     * @param string $mediaId
     * @param mixed  $to
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function sendNews(string $mediaId, $to = null)
    {
        return $this->sendMessage(new Media($mediaId, 'mpnews'), $to);
    }

    /**
     * Send a voice message.
     *
     * @param string $mediaId
     * @param mixed  $to
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function sendVoice(string $mediaId, $to = null)
    {
        return $this->sendMessage(new Media($mediaId, 'voice'), $to);
    }

    /**
     * Send a image message.
     *
     * @param mixed $mediaId
     * @param mixed $to
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function sendImage(string $mediaId, $to = null)
    {
        return $this->sendMessage(new Image($mediaId), $to);
    }

    /**
     * Send a video message.
     *
     * @param string $mediaId
     * @param mixed  $to
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function sendVideo(string $mediaId, $to = null)
    {
        return $this->sendMessage(new Media($mediaId, 'mpvideo'), $to);
    }

    /**
     * Send a card message.
     *
     * @param string $cardId
     * @param mixed  $to
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function sendCard(string $cardId, $to = null)
    {
        return $this->sendMessage(new Card($cardId), $to);
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
    public function previewText(string $message, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->previewMessage(new Text($message), $to, $by);
    }

    /**
     * Preview a news message.
     *
     * @param mixed  $mediaId message
     * @param string $to
     * @param string $by
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function previewNews(string $mediaId, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->previewMessage(new Media($mediaId, 'mpnews'), $to, $by);
    }

    /**
     * Preview a voice message.
     *
     * @param mixed  $mediaId message
     * @param string $to
     * @param string $by
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function previewVoice(string $mediaId, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->previewMessage(new Media($mediaId, 'voice'), $to, $by);
    }

    /**
     * Preview a image message.
     *
     * @param mixed  $mediaId message
     * @param string $to
     * @param string $by
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function previewImage(string $mediaId, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->previewMessage(new Image($mediaId), $to, $by);
    }

    /**
     * Preview a video message.
     *
     * @param mixed  $mediaId message
     * @param string $to
     * @param string $by
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function previewVideo(string $mediaId, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->previewMessage(new Media($mediaId, 'mpvideo'), $to, $by);
    }

    /**
     * Preview a card message.
     *
     * @param mixed  $cardId message
     * @param string $to
     * @param string $by
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function previewCard(string $cardId, $to, $by = self::PREVIEW_BY_OPENID)
    {
        return $this->previewMessage(new Card($cardId), $to, $by);
    }

    /**
     * @param \EasyWeChat\Kernel\Contracts\MessageInterface $message
     * @param string                                        $to
     * @param string                                        $by
     *
     * @return mixed
     */
    public function previewMessage(MessageInterface $message, $to = null, $by = self::PREVIEW_BY_OPENID)
    {
        $message = (new MessageBuilder())->message($message)->to($to)->buildForPreview($by);

        return $this->preview($message);
    }

    /**
     * @param \EasyWeChat\Kernel\Contracts\MessageInterface $message
     * @param null                                          $to
     *
     * @return mixed
     */
    public function sendMessage(MessageInterface $message, $to = null)
    {
        $message = (new MessageBuilder())->message($message)->to($to)->build();

        return $this->send($message);
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (strpos($method, 'ByName') > 0) {
            $method = strstr($method, 'ByName', true);

            if (method_exists($this, $method)) {
                array_push($args, self::PREVIEW_BY_NAME);

                return $this->$method(...$args);
            }
        }

        throw new \BadMethodCallException(sprintf('Method %s not exists.', $method));
    }
}
