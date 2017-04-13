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
 * AbstractOpenPlatform.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform\Api;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\OpenPlatform\AccessToken;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractOpenPlatform extends AbstractAPI
{
    /**
     * Request.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * AbstractOpenPlatform constructor.
     *
     * @param \EasyWeChat\OpenPlatform\AccessToken      $accessToken
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(AccessToken $accessToken, Request $request)
    {
        parent::__construct($accessToken);

        $this->request = $request;
    }

    /**
     * Get OpenPlatform AppId.
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->getAccessToken()->getAppId();
    }
}
