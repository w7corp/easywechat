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
 * MiniProgram.php.
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

namespace EasyWeChat\MiniProgram;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\MiniProgram\Material\Temporary;
use EasyWeChat\MiniProgram\Notice\Notice;
use EasyWeChat\MiniProgram\QRCode\QRCode;
use EasyWeChat\MiniProgram\Staff\Staff;
use EasyWeChat\MiniProgram\User\User;
use EasyWeChat\Support\Arr;

/**
 * Class MiniProgram.
 *
 * @property \EasyWeChat\MiniProgram\Server\Guard $server
 * @property \EasyWeChat\MiniProgram\User\User $user
 * @property \EasyWeChat\MiniProgram\Notice\Notice $notice
 * @property \EasyWeChat\MiniProgram\Staff\Staff $staff
 * @property \EasyWeChat\MiniProgram\QRCode\QRCode $qrcode
 * @property \EasyWeChat\MiniProgram\Material\Temporary $material_temporary
 */
class MiniProgram
{
    /**
     * Access Token.
     *
     * @var \EasyWeChat\MiniProgram\AccessToken
     */
    protected $accessToken;

    /**
     * Mini program config.
     *
     * @var array
     */
    protected $config;

    /**
     * Guard.
     *
     * @var \EasyWeChat\MiniProgram\Server\Guard
     */
    protected $server;

    /**
     * Components.
     *
     * @var array
     */
    protected $components = [
        'user' => User::class,
        'notice' => Notice::class,
        'staff' => Staff::class,
        'qrcode' => QRCode::class,
        'material_temporary' => Temporary::class,
    ];

    /**
     * MiniProgram constructor.
     *
     * @param \EasyWeChat\MiniProgram\Server\Guard $server
     * @param \EasyWeChat\MiniProgram\AccessToken  $accessToken
     * @param array                                $config
     */
    public function __construct($server, $accessToken, $config)
    {
        $this->server = $server;

        $this->accessToken = $accessToken;

        $this->config = $config;
    }

    /**
     * Magic get access.
     *
     * @param $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        if ($class = Arr::get($this->components, $name)) {
            return new $class($this->accessToken, $this->config);
        }

        throw new InvalidArgumentException("Property or component \"$name\" does not exists.");
    }
}
