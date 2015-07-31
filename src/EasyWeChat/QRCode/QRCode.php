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
 * 二维码.
 */
class QRCode
{
    /**
     * 应用ID.
     *
     * @var Http
     */
    protected $http;

    const DAY = 86400;
    const SCENE_QE_CARD = 'QR_CARD';             // 卡券
    const SCENE_QR_TEMPORARY = 'QR_SCENE';            // 临时
    const SCENE_QR_FOREVER = 'QR_LIMIT_SCENE';      // 永久
    const SCENE_QR_FOREVER_STR = 'QR_LIMIT_STR_SCENE';  // 永久的字符串参数值

    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/qrcode/create';
    const API_SHOW = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';

    /**
     * Constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http->setExpectedException('EasyWeChat\Tool\ToolHttpException');
    }

    /**
     * 永久二维码.
     *
     * @param int $sceneValue
     *
     * @return Collection
     */
    public function forever($sceneValue)
    {
        // 永久二维码时最大值为100000（目前参数只支持1--100000）
        if (is_int($sceneValue) && $sceneValue > 0 && $sceneValue < 100000) {
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
     * 临时二维码.
     *
     * @param int $sceneId
     * @param int $expireSeconds
     *
     * @return Collection
     */
    public function temporary($sceneId, $expireSeconds = null)
    {
        // 临时二维码时为32位非0整型
        $scene = ['scene_id' => intval($sceneId)];

        return $this->create(self::SCENE_QR_TEMPORARY, $scene, true, $expireSeconds);
    }

    /**
     * 创建卡券二维码.
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
        return $this->create(self::SCENE_QE_CARD, ['card' => $card]);
    }

    /**
     * 获取二维码.
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
     * 保存二维码.
     *
     * @param string $ticket
     * @param string $filename
     *
     * @return int
     */
    public function download($ticket, $filename)
    {
        return file_put_contents($filename, file_get_contents($this->show($ticket)));
    }

    /**
     * 创建二维码.
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
