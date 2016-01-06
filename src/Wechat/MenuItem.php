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
 * MenuItem.php.
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
use Overtrue\Wechat\Utils\MagicAttributes;

/**
 * 菜单项.
 *
 * @property array $sub_button
 */
class MenuItem extends MagicAttributes
{
    /**
     * 实例化菜单.
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
            switch ($type) {
                case 'view':
                    $key = 'url';
                    break;
                case 'media_id':
                    // no break
                case 'view_limited':
                    $key = 'media_id';
                    break;
                default:
                    $key = 'key';
            }
            $this->with($key, $property);
        }
    }

    /**
     * 设置子菜单.
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
     * 添加子菜单.
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
