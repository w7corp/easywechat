<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Base;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Get callback ip.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getCallbackIp()
    {
        return $this->httpGet('cgi-bin/getcallbackip');
    }
}
