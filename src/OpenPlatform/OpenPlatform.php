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
 * OpenPlatform.php.
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

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Support\Arr;

/**
 * Class OpenPlatform.
 *
 * @property \EasyWeChat\OpenPlatform\Guard $server
 * @property \EasyWeChat\OpenPlatform\Components\PreAuthCode $pre_auth
 * @property \EasyWeChat\OpenPlatform\Components\Authorizer $authorizer
 */
class OpenPlatform
{
    /**
     * Server guard.
     *
     * @var Guard
     */
    protected $server;

    /**
     * OpenPlatform component access token.
     *
     * @var AccessToken
     */
    protected $access_token;

    /**
     * OpenPlatform config.
     *
     * @var array
     */
    protected $config;

    /**
     * Components.
     *
     * @var array
     */
    private $components = [
        'pre_auth' => Components\PreAuthCode::class,
        'authorizer' => Components\Authorizer::class,
    ];

    /**
     * OpenPlatform constructor.
     *
     * @param Guard $server
     * @param $access_token
     * @param array $config
     * @param $verifyTicket
     */
    public function __construct(Guard $server, $access_token, $config, $verifyTicket)
    {
        $this->server = $server;
        $this->server->setVerifyTicket($verifyTicket);

        $this->access_token = $access_token;
        $this->access_token->setVerifyTicket($verifyTicket);

        $this->config = $config;
    }

    /**
     * Magic get access.
     *
     * @param $name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        if ($class = Arr::get($this->components, $name)) {
            return new $class($this->access_token, $this->config);
        }

        throw new InvalidArgumentException("Property or component \"$name\" does not exists.");
    }
}
