<?php

/**
 * User.php.
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

namespace EasyWeChat\OAuth;

use EasyWeChat\Support\Attribute;

/**
 * Class User.
 */
class User extends Attribute
{
    /**
     * Get open id of user.
     *
     * @return string
     */
    public function getOpenId()
    {
        return $this->get('openid');
    }

    /**
     * Get nickname of user.
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->get('nickname');
    }

    /**
     * Get username(alias of getNickname).
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getNickname();
    }

    /**
     * Get avatar of user.
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->get('headimgurl');
    }

    /**
     * Set OAuth access_token.
     *
     * @param string $token
     *
     * @return User
     */
    public function setToken($token)
    {
        return $this->set('token', $token);
    }

    /**
     * Set refresh token.
     *
     * @param string $token
     *
     * @return User
     */
    public function setRefreshToken($token)
    {
        return $this->set('refresh_token', $token);
    }
}
