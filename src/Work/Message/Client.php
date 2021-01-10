<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Message;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Messages\Message;

class Client extends BaseClient
{
    /**
     * @param string|\EasyWeChat\Kernel\Messages\Message $message
     *
     * @return \EasyWeChat\Work\Message\Messenger
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function message($message)
    {
        return (new Messenger($this))->message($message);
    }

    /**
     * @param array $message
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(array $message)
    {
        return $this->httpPostJson('cgi-bin/message/send', $message);
    }
}
