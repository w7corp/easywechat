<?php

declare(strict_types=1);

namespace EasyWeChat\Work\MiniProgram\Auth;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Get session info by code.
     *
     * @param string $code
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function session(string $code)
    {
        $params = [
            'js_code' => $code,
            'grant_type' => 'authorization_code',
        ];

        return $this->httpGet('cgi-bin/miniprogram/jscode2session', $params);
    }
}
