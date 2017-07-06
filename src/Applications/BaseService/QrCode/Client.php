<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\BaseService\QrCode;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    const DAY = 86400;
    const SCENE_MAX_VALUE = 100000;
    const SCENE_QR_CARD = 'QR_CARD';
    const SCENE_QR_TEMPORARY = 'QR_SCENE';
    const SCENE_QR_FOREVER = 'QR_LIMIT_SCENE';
    const SCENE_QR_FOREVER_STR = 'QR_LIMIT_STR_SCENE';

    /**
     * Create forever.
     *
     * @param int $sceneValue
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function forever($sceneValue)
    {
        if (is_int($sceneValue) && $sceneValue > 0 && $sceneValue < self::SCENE_MAX_VALUE) {
            $type = self::SCENE_QR_FOREVER;
            $sceneKey = 'scene_id';
        } else {
            $type = self::SCENE_QR_FOREVER_STR;
            $sceneKey = 'scene_str';
        }

        $scene = [$sceneKey => $sceneValue];

        return $this->create($type, $scene, false);
    }

    /**
     * Create temporary.
     *
     * @param string $sceneId
     * @param null   $expireSeconds
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function temporary($sceneId, $expireSeconds = null)
    {
        $scene = ['scene_id' => $sceneId];

        return $this->create(self::SCENE_QR_TEMPORARY, $scene, true, $expireSeconds);
    }

    /**
     * Create QrCode for card.
     *
     * @param array $card
     *
     * {
     *    "card_id": "pFS7Fjg8kV1IdDz01r4SQwMkuCKc",
     *    "code": "198374613512",
     *    "openid": "oFS7Fjl0WsZ9AMZqrI80nbIq8xrA",
     *    "expire_seconds": "1800"，
     *    "is_unique_code": false , "outer_id" : 1
     *  }
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function card($card)
    {
        return $this->create(self::SCENE_QR_CARD, ['card' => $card]);
    }

    /**
     * Return url for ticket.
     *
     * @param string $ticket
     *
     * @return string
     */
    public function url($ticket)
    {
        return sprintf('cgi-bin/showqrcode?ticket=%s', $ticket);
    }

    /**
     * Create a QrCode.
     *
     * @param string $actionName
     * @param array  $actionInfo
     * @param bool   $temporary
     * @param int    $expireSeconds
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    protected function create($actionName, $actionInfo, $temporary = true, $expireSeconds = null)
    {
        $expireSeconds !== null || $expireSeconds = 7 * self::DAY;

        $params = [
            'action_name' => $actionName,
            'action_info' => ['scene' => $actionInfo],
        ];

        if ($temporary) {
            $params['expire_seconds'] = min($expireSeconds, 30 * self::DAY);
        }

        return $this->httpPostJson('cgi-bin/qrcode/create', $params);
    }
}
