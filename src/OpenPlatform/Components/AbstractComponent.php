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
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform\Components;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Exception;
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
     * AppId, component app id.
     *
     * @var string
     */
    private $appId;

    /**
     * Request.
     *
     * @var Request
     */
    protected $request;

    /**
     * AbstractComponent constructor.
     *
     * @param AccessToken $accessToken
     * @param array       $config
     * @param $request
     */
    public function __construct($accessToken, array $config, $request = null)
    {
        parent::__construct($accessToken);
        $this->config = $config;
        $this->request = $request ?: Request::createFromGlobals();
    }

    /**
     * Get AppId.
     *
     * @return string
     *
     * @throws Exception when app id is not present
     */
    public function getAppId()
    {
        if ($this->appId) {
            return $this->appId;
        }

        if (isset($this->config['open_platform'])) {
            $this->appId = $this->config['open_platform']['app_id'];
        } else {
            $this->appId = $this->config['app_id'];
        }

        if (empty($this->appId)) {
            throw new Exception('App Id is not present.');
        }

        return $this->appId;
    }

    /**
     * Set AppId.
     *
     * @param string $appId
     *
     * @return $this
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }
}
