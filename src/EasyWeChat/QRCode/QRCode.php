<?php

/**
 * QRCode.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\QRCode;

use EasyWeChat\Core\Http;
use EasyWeChat\Support\Collection;

/**
 * Class QRCode.
 */
class QRCode
{
    /**
     * Http Client.
     *
     * @var Http
     */
    protected $http;

    const DAY = 86400;
    const SCENE_MAX_VALUE = 100000;
    const SCENE_QR_CARD = 'QR_CARD';
    const SCENE_QR_TEMPORARY = 'QR_SCENE';
    const SCENE_QR_FOREVER = 'QR_LIMIT_SCENE';
    const SCENE_QR_FOREVER_STR = 'QR_LIMIT_STR_SCENE';

    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/qrcode/create';
    const API_SHOW = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';

    /**
     * Constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http->setExpectedException(QRCodeHttpException::class);
    }

    /**
     * Create forever.
     *
     * @param int $sceneValue
     *
     * @return Collection
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
     * @param int $sceneId
     * @param int $expireSeconds
     *
     * @return Collection
     */
    public function temporary($sceneId, $expireSeconds = null)
    {
        $scene = ['scene_id' => intval($sceneId)];

        return $this->create(self::SCENE_QR_TEMPORARY, $scene, true, $expireSeconds);
    }

    /**
     * Create QRCode for card.
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
     * @return Collection
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
    public function show($ticket)
    {
        return self::API_SHOW."?ticket={$ticket}";
    }

    /**
     * Create a QRCode.
     *
     * @param string $actionName
     * @param array  $actionInfo
     * @param bool   $temporary
     * @param int    $expireSeconds
     *
     * @return Collection
     */
    protected function create($actionName, $actionInfo, $temporary = true, $expireSeconds = null)
    {
        $expireSeconds !== null || $expireSeconds = 7 * self::DAY;

        $params = [
                   'action_name' => $actionName,
                   'action_info' => ['scene' => $actionInfo],
                  ];

        if ($temporary) {
            $params['expire_seconds'] = min($expireSeconds, 7 * self::DAY);
        }

        return new Collection($this->http->json(self::API_CREATE, $params));
    }
}
