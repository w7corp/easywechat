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
 * UserServiceProvider.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015
 *
 * @link      https://github.com/overtrue/wechat
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Foundation\ServiceProviders;

use EasyWeChat\User\Group;
use EasyWeChat\User\Tag;
use EasyWeChat\User\User;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class UserServiceProvider.
 */
class UserServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['user'] = function ($pimple) {
            return new User($pimple['access_token']);
        };

        $group = function ($pimple) {
            return new Group($pimple['access_token']);
        };

        $tag = function ($pimple) {
            return new Tag($pimple['access_token']);
        };

        $pimple['user_group'] = $group;
        $pimple['user.group'] = $group;

        $pimple['user_tag'] = $tag;
        $pimple['user.tag'] = $tag;
    }
}
