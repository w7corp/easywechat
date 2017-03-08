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
use Pimple\Container;

/**
 * Class OpenPlatform.
 *
 * @property \EasyWeChat\OpenPlatform\Guard $server
 * @property \EasyWeChat\OpenPlatform\Components\PreAuthCode $pre_auth
 * @property \EasyWeChat\OpenPlatform\AccessToken $access_token
 * @property \EasyWeChat\OpenPlatform\AuthorizerToken $authorizer_token;
 * @property \EasyWeChat\OpenPlatform\Authorization $authorization;
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
     * Container in the scope of the open platform.
     *
     * @var Container
     */
    protected $container;

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
     */
    public function __construct(Guard $server, $access_token, $config)
    {
        $this->server = $server;
        $this->access_token = $access_token;
        $this->config = $config;
    }

    /**
     * Sets the container for use of the platform.
     *
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
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

        if ($instance = $this->container->offsetGet("open_platform.{$name}")) {
            return $instance;
        }

        throw new InvalidArgumentException("Property or component \"$name\" does not exists.");
    }
}
