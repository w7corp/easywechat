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
 * Shelf.php.
 *
 * Part of Overtrue\Wechat\Shop\Data.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a939638621 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 *
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Shop\Data;

use Overtrue\Wechat\Shop\Foundation\ShopsException;
use Overtrue\Wechat\Utils\MagicAttributes;

class Shelf extends MagicAttributes
{
    /**
     * 控件1.
     *
     * @param int $count
     * @param int $groupId
     *
     * @return $this
     */
    public function controlOne($count, $groupId)
    {
        $this->attributes[] = array(
            'group_info' => array(
                'filter' => array(
                    'count' => $count,
                ),
                'group_id' => $groupId,
            ),
            'eid' => count($this->attributes) + 1,
        );

        return $this;
    }

    /**
     * 控件2.
     *
     * @param array $groupId
     *
     * @return Shelf $this
     *
     * @throws ShopsException
     */
    public function controlTwo(array $groupId)
    {
        if (count($groupId) > 4) {
            throw new ShopsException('错误个数');
        }

        $groupsData = array();

        foreach ($groupId as $v) {
            $groupsData[] = array('group_id' => $v);
        }

        $this->attributes[] = array(
            'group_infos' => array(
                'groups' => $groupsData,
            ),
            'eid' => count($this->attributes) + 1,
        );

        return $this;
    }

    /**
     * 控件3.
     *
     * @param string $groupId
     * @param string $img
     *
     * @return Shelf $this
     */
    public function controlThree($groupId, $img)
    {
        $this->attributes[] = array(
            'group_info' => array(
                'group_id' => $groupId,
                'img' => $img,
            ),
            'eid' => count($this->attributes) + 1,
        );

        return $this;
    }

    /**
     * 控件4.
     *
     * @param array $groups
     *
     * @return $this
     *
     * @throws ShopsException
     */
    public function controlFour(array $groups)
    {
        if (count($groups) > 3) {
            throw new ShopsException('个数错误');
        }

        $groupsData = array();

        foreach ($groups as $k => $v) {
            if (count($v) != 2) {
                throw new ShopsException('个数错误');
            }

            $keys = array_keys($v);

//            [
//                ['1','images'],
//                ['1','images'],
//                ['1','images']
//            ];

            if (is_numeric($keys[0]) && is_numeric($keys[1])) {
                $values = array_values($v);

                $groupsData[] = array(
                    'group_id' => $values[0],
                    'img' => $values[1],
                );
            }

//            [
//                [ 'group_id'=>'1','img'=>'images' ],
//                [ 'group_id'=>'1','img'=>'images' ],
//                [ 'group_id'=>'1','img'=>'images' ]
//            ];

            if ($keys[0] == 'group_id' && $keys[1] == 'img') {
                $groupsData = $groups;
            }
        }

        $this->attributes[] = array(
            'group_infos' => array(
                'groups' => $groupsData,
            ),
            'eid' => count($this->attributes) + 1,
        );

        return $this;
    }

    /**
     * 控件5.
     *
     * @param array  $groups
     * @param string $imgBackground
     *
     * @return $this
     */
    public function controlFive(array $groups, $imgBackground)
    {
        $groupsData = array();

        foreach ($groups as $v) {
            $groupsData[] = array('group_id' => $v);
        }

        $this->attributes[] = array(
            'group_infos' => array(
                'groups' => $groupsData,
                'img_background' => $imgBackground,
            ),
            'eid' => count($this->attributes) + 1,
        );

        return $this;
    }
}
