<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\WeWork\Message;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Messages\Message;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return \EasyWeChat\WeWork\Message\Messenger
     */
    public function message(Message $message)
    {
        return (new Messenger($this))->message($message);
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
