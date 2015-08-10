<?php

/**
 * Menu.php.
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

namespace EasyWeChat\Menu;

use Closure;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Http;
use EasyWeChat\Support\Collection;

/**
 * Class Menu.
 */
class Menu
{
    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    const API_GET = 'https://api.weixin.qq.com/cgi-bin/menu/get';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delete';
    const API_QUERY = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info';

    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    /**
     * Constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http->setExpectedException(MenuHttpException::class);
    }

    /**
     * Set menu.
     *
     * @param array $menus
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function set($menus)
    {
        if ($menus instanceof Closure) {
            $menus = $menus($this);
        }

        if (!is_array($menus)) {
            throw new InvalidArgumentException('buttons must be an array or Closure returns an array.');
        }

        $menus = $this->extractMenus($menus);

        return $this->http->json(self::API_CREATE, ['button' => $menus]);
    }

    /**
     * Get menus.
     *
     * @return array
     */
    public function get()
    {
        $menus = $this->http->get(self::API_GET);

        return empty($menus['menu']['button']) ? [] : $menus['menu']['button'];
    }

    /**
     * Delete.
     *
     * @return bool
     */
    public function delete()
    {
        return $this->http->get(self::API_DELETE);
    }

    /**
     * Get current menus setting.
     *
     * @return array
     */
    public function current()
    {
        return $this->http->get(self::API_QUERY);
    }

    /**
     * Extract menus to array.
     *
     * @param array|Collection $menus
     *
     * @return array
     */
    protected function extractMenus($menus)
    {
        if ($menus instanceof Collection) {
            $menus = $menus->toArray();
        }

        foreach ($menus as $key => $menu) {
            if ($menu instanceof Collection) {
                $menus[$key] = $menu->toArray();
            }

            if (!empty($menu['sub_button'])) {
                $menus[$key]['sub_button'] = $this->extractMenus($menu['sub_button']);
            }
        }

        return $menus;
    }
}
