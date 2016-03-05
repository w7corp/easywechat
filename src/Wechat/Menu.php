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
 * Part of Overtrue\Wechat.
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

namespace Overtrue\Wechat;

use Closure;

/**
 * 菜单.
 *
 * @property array $sub_button
 */
class Menu
{
    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    const API_GET = 'https://api.weixin.qq.com/cgi-bin/menu/get';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delete';
    const API_QUERY = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info';
    const API_CONDITIONAL_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/addconditional';
    const API_CONDITIONAL_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delconditional';
    const API_CONDITIONAL_TEST = 'https://api.weixin.qq.com/cgi-bin/menu/trymatch';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 设置菜单.
     *
     * @return bool
     */
    public function set($menus)
    {
        $menus = $this->extractMenus($menus);

        $this->http->jsonPost(self::API_CREATE, array('button' => $menus));

        return true;
    }

    /**
     * 获取菜单.
     *
     * @return array
     */
    public function get()
    {
        return $this->http->get(self::API_GET);
    }

    /**
     * 删除菜单.
     *
     * @return bool
     */
    public function delete()
    {
        $this->http->get(self::API_DELETE);

        return true;
    }

    /**
     * 获取菜单【查询接口，能获取到任意方式设置的菜单】.
     *
     * @return array
     */
    public function current()
    {
        $menus = $this->http->get(self::API_QUERY);

        return empty($menus) ? array() : $menus;
    }

    /**
     * 添加个性化的菜单.
     *
     * @param mixed $menus
     * @param array $condition
     */
    public function addConditional($menus, array $condition)
    {
        $menus = $this->extractMenus($menus);

        $this->http->jsonPost(self::API_CONDITIONAL_CREATE, array('button' => $menus, 'matchrule' => $condition));

        return true;
    }

    /**
     * 测试菜单.
     *
     * @param string $userId
     *
     * @return bool
     */
    public function test($userId)
    {
        return $this->http->post(self::API_CONDITIONAL_TEST, array('user_id' => $userId));
    }

    /**
     * 按菜单ID删除菜单.
     *
     * @param int $menuId
     *
     * @return bool
     */
    public function deleteById($menuId)
    {
        $this->http->post(self::API_CONDITIONAL_DELETE, array('menuid' => $menuId));

        return true;
    }

    /**
     * 转menu为数组.
     *
     * @param mixed $menus
     *
     * @return array
     */
    protected function extractMenus($menus)
    {
        if ($menus instanceof Closure) {
            $menus = $menus($this);
        }

        if (!is_array($menus)) {
            throw new Exception('子菜单必须是数组或者匿名函数返回数组', 1);
        }

        foreach ($menus as $key => $menu) {
            $menus[$key] = $menu->toArray();

            if ($menu->sub_button) {
                $menus[$key]['sub_button'] = $this->extractMenus($menu->sub_button);
            }
        }

        return $menus;
    }
}
