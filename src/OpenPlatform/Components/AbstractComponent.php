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
 * AbstractComponent.php.
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

namespace EasyWeChat\OpenPlatform\Components;

use EasyWeChat\Core\AbstractAPI;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractComponent extends AbstractAPI
{
    /**
     * Config.
     *
     * @var array
     */
    protected $config;

    /**
     * Request.
     *
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * AbstractComponent constructor.
     *
     * @param \EasyWeChat\Core\AccessToken $accessToken
     * @param array                        $config
     * @param $request
     */
    public function __construct($accessToken, array $config, $request = null)
    {
        parent::__construct($accessToken);
        $this->config = $config;
        $this->request = $request ?: Request::createFromGlobals();
    }
}
