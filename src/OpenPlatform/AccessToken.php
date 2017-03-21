<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * AccessToken.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform;

use Doctrine\Common\Cache\Cache;
use EasyWeChat\Core\AccessToken as WechatAccessToken;
use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\OpenPlatform\Traits\VerifyTicketTrait;

class AccessToken extends WechatAccessToken
{
    use VerifyTicketTrait;

    /**
     * API.
     */
    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';

    /**
     * {@inheritdoc}.
     */
    protected $queryName = 'component_access_token';

    /**
     * {@inheritdoc}.
     */
    protected $tokenJsonKey = 'component_access_token';

    /**
     * {@inheritdoc}.
     */
    protected $prefix = 'easywechat.open_platform.component_access_token.';

    /**
     * AccessToken constructor.
     *
     * @param string       $appId
     * @param string       $secret
     * @param Cache        $cache
     * @param VerifyTicket $verifyTicket
     */
    public function __construct($appId, $secret, VerifyTicket $verifyTicket, Cache $cache = null)
    {
        parent::__construct($appId, $secret, $cache);

        $this->setVerifyTicket($verifyTicket);
    }

    /**
     * {@inheritdoc}.
     */
    public function getTokenFromServer()
    {
        $data = [
            'component_appid' => $this->appId,
            'component_appsecret' => $this->secret,
            'component_verify_ticket' => $this->verifyTicket->getTicket(),
        ];

        $http = $this->getHttp();

        $token = $http->parseJSON($http->json(self::API_TOKEN_GET, $data));

        if (empty($token[$this->tokenJsonKey])) {
            throw new HttpException('Request ComponentAccessToken fail. response: '.json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
    }
}
