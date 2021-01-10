<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\AutoReply;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Get current auto reply settings.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function current()
    {
        return $this->httpGet('cgi-bin/get_current_autoreply_info');
    }
}
