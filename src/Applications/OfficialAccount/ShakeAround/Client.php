<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OfficialAccount\ShakeAround;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * Register shake around.
     *
     * @param string $name
     * @param string $tel
     * @param string $email
     * @param string $industryId
     * @param array  $certUrls
     * @param string $reason
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function register($name, $tel, $email, $industryId, array $certUrls, $reason = '')
    {
        $params = [
            'name' => $name,
            'phone_number' => strval($tel),
            'email' => $email,
            'industry_id' => $industryId,
            'qualification_cert_urls' => $certUrls,
        ];

        if ($reason !== '') {
            $params['apply_reason'] = $reason;
        }

        return $this->httpPostJson('shakearound/account/register', $params);
    }

    /**
     * Get audit status.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function getStatus()
    {
        return $this->httpGet('shakearound/account/auditstatus');
    }

    /**
     * Get shake info.
     *
     * @param string $ticket
     * @param int    $needPoi
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function getShakeInfo($ticket, $needPoi = null)
    {
        $params = [
            'ticket' => $ticket,
        ];

        if ($needPoi !== null) {
            $params['need_poi'] = intval($needPoi);
        }

        return $this->httpGet('shakearound/user/getshakeinfo');
    }
}
