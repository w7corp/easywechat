<?php

namespace Overtrue\Wechat\Services;

use Closure;
use Overtrue\Wechat\Utils\MagicAccess;

class MenuItem extends MagicAccess
{
    /**
     * 实例化菜单
     *
     * @param string $name
     * @param string $type
     * @param string $property
     */
    public function __construct($name, $type = null, $property = null)
    {
        $this->attributes['name'] = $name;
        $type && $this->attributes['type'] = $type;

        if ($property) {
            $key = ($type == 'view') ? 'url' : 'key';
            $this->attributes[$key] = $property;
        }
    }

    /**
     * 设置子菜单
     *
     * @param array $buttons
     *
     * @return MenuItem;
     */
    public function buttons($buttons)
    {
        if ($buttons instanceof Closure) {
            $buttons = $buttons($this);
        }

        if (!is_array($buttons)) {
            throw new Exception("子菜单必须是数组或者匿名函数返回数组", 1);
        }

        $this->attributes['sub_button'] = $buttons;

        return $this;
    }
}