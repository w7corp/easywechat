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
 * Group.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue
 * @see       http://overtrue.me
 */

namespace EasyWeChat\ShakeAround;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class Group.
 */
class Group extends AbstractAPI
{
    const API_ADD = 'https://api.weixin.qq.com/shakearound/device/group/add';
    const API_UPDATE = 'https://api.weixin.qq.com/shakearound/device/group/update';
    const API_DELETE = 'https://api.weixin.qq.com/shakearound/device/group/delete';
    const API_GET_LIST = 'https://api.weixin.qq.com/shakearound/device/group/getlist';
    const API_GET_DETAIL = 'https://api.weixin.qq.com/shakearound/device/group/getdetail';
    const API_ADD_DEVICE = 'https://api.weixin.qq.com/shakearound/device/group/adddevice';
    const API_DELETE_DEVICE = 'https://api.weixin.qq.com/shakearound/device/group/deletedevice';

    /**
     * Add device group.
     *
     * @param string $name
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function add($name)
    {
        $params = [
            'group_name' => $name,
        ];

        return $this->parseJSON('json', [self::API_ADD, $params]);
    }

    /**
     * Update a device group name.
     *
     * @param int    $groupId
     * @param string $name
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function update($groupId, $name)
    {
        $params = [
            'group_id' => intval($groupId),
            'group_name' => $name,
        ];

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    /**
     * Delete device group.
     *
     * @param int $groupId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function delete($groupId)
    {
        $params = [
            'group_id' => intval($groupId),
        ];

        return $this->parseJSON('json', [self::API_DELETE, $params]);
    }

    /**
     * List all device groups.
     *
     * @param int $begin
     * @param int $count
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function lists($begin, $count)
    {
        $params = [
            'begin' => intval($begin),
            'count' => intval($count),
        ];

        return $this->parseJSON('json', [self::API_GET_LIST, $params]);
    }

    /**
     * Get details of a device group.
     *
     * @param int $groupId
     * @param int $begin
     * @param int $count
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getDetails($groupId, $begin, $count)
    {
        $params = [
            'group_id' => intval($groupId),
            'begin' => intval($begin),
            'count' => intval($count),
        ];

        return $this->parseJSON('json', [self::API_GET_DETAIL, $params]);
    }

    /**
     * Add  one or more devices to a device group.
     *
     * @param int   $groupId
     * @param array $deviceIdentifiers
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function addDevice($groupId, array $deviceIdentifiers)
    {
        $params = [
            'group_id' => intval($groupId),
            'device_identifiers' => $deviceIdentifiers,
        ];

        return $this->parseJSON('json', [self::API_ADD_DEVICE, $params]);
    }

    /**
     * Remove one or more devices from a device group.
     *
     * @param int   $groupId
     * @param array $deviceIdentifiers
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function removeDevice($groupId, array $deviceIdentifiers)
    {
        $params = [
            'group_id' => intval($groupId),
            'device_identifiers' => $deviceIdentifiers,
        ];

        return $this->parseJSON('json', [self::API_DELETE_DEVICE, $params]);
    }
}
