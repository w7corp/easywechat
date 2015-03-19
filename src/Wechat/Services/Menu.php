<?php

namespace Overtrue\Wechat\Services;

use Closure;
use Overtrue\Wechat\Wechat;

/**
 * @property array $sub_button
 */
class Menu
{
    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    const API_GET    = 'https://api.weixin.qq.com/cgi-bin/menu/get';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delete';


    /**
     * 生成菜单项
     *
     * @param string $name
     * @param string $type
     * @param string $property
     *
     * @return MenuItem
     */
    static public function make($name, $type = null, $property = null)
    {
        return new MenuItem($name, $type, $property);
    }

    /**
     * 设置菜单
     *
     * @return boolean
     */
    public function set($menus)
    {
        if ($menus instanceof Closure) {
            $menus = $menus($this);
        }

        if (!is_array($menus)) {
            throw new Exception("子菜单必须是数组或者匿名函数返回数组", 1);
        }

        $menus = $this->extractMenus($menus);

        Wechat::request('POST', self::API_CREATE, array('button' => $menus), array('json' => true));

        return true;
    }

    /**
     * 获取菜单
     *
     * @return array
     */
    public function get()
    {
        $menus = Wechat::request('GET', self::API_GET);

        return empty($menus['menu']['button']) ? array() : $menus['menu']['button'];
    }

    /**
     * 删除菜单
     *
     * @return boolean
     */
    public function delete()
    {
        Wechat::request('GET', self::API_DELETE);

        return true;
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

    /**
     * 访问不存在的方法
     *
     * @param string $method
     * @param array  $args
     *
     * @return MenuItem
     */
    public function __call($method, $args)
    {
        return self::__callStatic($method, $args);
    }

    /**
     * 静态访问
     *
     * @param string $method
     * @param array  $args
     *
     * @return MenuItem
     */
    static public function __callStatic($method, $args)
    {
        if (count($args) > 1) {
            list($name, $property) = $args;
            $args = array($name, $method, $property);
        } else {
            array_push($args, $method);
        }

        return call_user_func_array('self::make', $args);
    }
}
