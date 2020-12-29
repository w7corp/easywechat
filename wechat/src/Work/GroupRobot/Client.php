<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\GroupRobot;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Work\GroupRobot\Messages\Message;

/**
 * Class Client.
 *
 * @author her-cat <i@her-cat.com>
 */
class Client extends BaseClient
{
    /**
     * @param string|Message $message
     *
     * @return Messenger
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function message($message)
    {
        return (new Messenger($this))->message($message);
    }

    /**
     * @param string $key
     * @param array  $message
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(string $key, array $message)
    {
        $this->accessToken = null;

        return $this->httpPostJson('cgi-bin/webhook/send', $message, ['key' => $key]);
    }
}
