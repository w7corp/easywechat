<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\Message;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Messages\Message;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * @param \EasyWeChat\Messages\Message $message
     *
     * @return \EasyWeChat\Applications\WeWork\Message\MessageBuilder
     */
    public function message(Message $message)
    {
        return (new MessageBuilder())->message($message);
    }

    /**
     * @param array $message
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send(array $message)
    {
        return $this->httpPostJson('message/send', $message);
    }
}
