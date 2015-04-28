<?php

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\MagicAttributes;
use Closure;

/**
 * 菜单项
 *
 * @property array $sub_button
 */
class MenuItem extends MagicAttributes
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
        $this->with('name', $name);

        $type !== null && $this->with('type', $type);

        if ($property !== null) {
            $key = ($type === 'view') ? 'url' : 'key';
            $this->with($key, $property);
        }
    }

    /**
     * 设置子菜单
     *
     * @param array $buttons
     *
     * @return MenuItem
     */
    public function buttons($buttons)
    {
        if ($buttons instanceof Closure) {
            $buttons = $buttons($this);
        }

        if (!is_array($buttons)) {
            throw new Exception('子菜单必须是数组或者匿名函数返回数组', 1);
        }

        $this->with('sub_button', $buttons);

        return $this;
    }

    /**
     * 添加子菜单
     *
     * @param MenuItem $button
     */
    public function button(MenuItem $button)
    {
        $subButtons = $this->sub_button;

        $subButtons[] = $button;

        $this->with('sub_button', $subButtons);
    }
}
