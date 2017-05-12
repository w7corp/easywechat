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
 * Menu.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Menu;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class Menu.
 */
class Menu extends AbstractAPI
{
    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    const API_GET = 'https://api.weixin.qq.com/cgi-bin/menu/get';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delete';
    const API_QUERY = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info';
    const API_CONDITIONAL_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/addconditional';
    const API_CONDITIONAL_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delconditional';
    const API_CONDITIONAL_TEST = 'https://api.weixin.qq.com/cgi-bin/menu/trymatch';

    /**
     * Get all menus.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function all()
    {
        return $this->parseJSON('get', [self::API_GET]);
    }

    /**
     * Get current menus.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function current()
    {
        return $this->parseJSON('get', [self::API_QUERY]);
    }

    /**
     * Add menu.
     *
     * @param array $buttons
     * @param array $matchRule
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function add(array $buttons, array $matchRule = [])
    {
        if (!empty($matchRule)) {
            return $this->parseJSON('json', [self::API_CONDITIONAL_CREATE, [
                'button' => $buttons,
                'matchrule' => $matchRule,
            ]]);
        }

        return $this->parseJSON('json', [self::API_CREATE, ['button' => $buttons]]);
    }

    /**
     * Destroy menu.
     *
     * @param int $menuId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function destroy($menuId = null)
    {
        if ($menuId !== null) {
            return $this->parseJSON('json', [self::API_CONDITIONAL_DELETE, ['menuid' => $menuId]]);
        }

        return  $this->parseJSON('get', [self::API_DELETE]);
    }

    /**
     * Test conditional menu.
     *
     * @param string $userId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function test($userId)
    {
        return $this->parseJSON('json', [self::API_CONDITIONAL_TEST, ['user_id' => $userId]]);
    }
}
