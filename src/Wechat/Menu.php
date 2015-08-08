<?php
/**
 * Menu.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

use Closure;

/**
 * 菜单
 *
 * @property array $sub_button
 */
class Menu
{
    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    const API_GET    = 'https://api.weixin.qq.com/cgi-bin/menu/get';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delete';
    const API_QUERY  = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 设置菜单
     *
     * @return bool
     */
    public function set($menus)
    {
        if ($menus instanceof Closure) {
            $menus = $menus($this);
        }

        if (!is_array($menus)) {
            throw new Exception('子菜单必须是数组或者匿名函数返回数组', 1);
        }

        $menus = $this->extractMenus($menus);

        $this->http->jsonPost(self::API_CREATE, array('button' => $menus));

        return true;
    }

    /**
     * 获取菜单
     *
     * @return array
     */
    public function get()
    {
        $menus = $this->http->get(self::API_GET);

        return empty($menus['menu']['button']) ? array() : $menus['menu']['button'];
    }

    /**
     * 删除菜单
     *
     * @return bool
     */
    public function delete()
    {
        $this->http->get(self::API_DELETE);

        return true;
    }

    /**
     * 获取菜单【查询接口，能获取到任意方式设置的菜单】
     *
     * @return array
     */
    public function current()
    {
        $menus = $this->http->get(self::API_QUERY);
        return empty($menus) ? array() : $menus;
    }

    /**
     * 转menu为数组
     *
     * @param array $menus
     *
     * @return array
     */
    protected function extractMenus(array $menus)
    {
        foreach ($menus as $key => $menu) {
            $menus[$key] = $menu->toArray();

            if ($menu->sub_button) {
                $menus[$key]['sub_button'] = $this->extractMenus($menu->sub_button);
            }
        }

        return $menus;
    }
}
