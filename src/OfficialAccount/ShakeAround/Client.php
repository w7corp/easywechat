<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\ShakeAround;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param array $data
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function register($data)
    {
        return $this->httpPostJson('shakearound/account/register', $data);
    }

    /**
     * Get audit status.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function status()
    {
        return $this->httpGet('shakearound/account/auditstatus');
    }

    /**
     * Get shake info.
     *
     * @param string $ticket
     * @param bool   $needPoi
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function user(string $ticket, bool $needPoi = false)
    {
        $params = [
            'ticket' => $ticket,
        ];

        if ($needPoi) {
            $params['need_poi'] = 1;
        }

        return $this->httpPostJson('shakearound/user/getshakeinfo', $params);
    }

    /**
     * @param string $ticket
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function userWithPoi(string $ticket)
    {
        return $this->user($ticket, true);
    }
}
