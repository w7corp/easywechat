<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\ShakeAround;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class GroupClient.
 *
 * @author allen05ren <allen05ren@outlook.com>
 */
class GroupClient extends BaseClient
{
    /**
     * Add device group.
     *
     * @param string $name
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function create($name)
    {
        $params = [
            'group_name' => $name,
        ];

        return $this->httpPostJson('shakearound/device/group/add', $params);
    }

    /**
     * Update a device group name.
     *
     * @param int    $groupId
     * @param string $name
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function update($groupId, $name)
    {
        $params = [
            'group_id' => intval($groupId),
            'group_name' => $name,
        ];

        return $this->httpPostJson('shakearound/device/group/update', $params);
    }

    /**
     * Delete device group.
     *
     * @param int $groupId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete($groupId)
    {
        $params = [
            'group_id' => intval($groupId),
        ];

        return $this->httpPostJson('shakearound/device/group/delete', $params);
    }

    /**
     * List all device groups.
     *
     * @param int $begin
     * @param int $count
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function lists($begin, $count)
    {
        $params = [
            'begin' => intval($begin),
            'count' => intval($count),
        ];

        return $this->httpPostJson('shakearound/device/group/getlist', $params);
    }

    /**
     * Get detail of a device group.
     *
     * @param int $groupId
     * @param int $begin
     * @param int $count
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function get($groupId, $begin, $count)
    {
        $params = [
            'group_id' => intval($groupId),
            'begin' => intval($begin),
            'count' => intval($count),
        ];

        return $this->httpPostJson('shakearound/device/group/getdetail', $params);
    }

    /**
     * Add  one or more devices to a device group.
     *
     * @param int   $groupId
     * @param array $deviceIdentifiers
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function addDevice($groupId, array $deviceIdentifiers)
    {
        $params = [
            'group_id' => intval($groupId),
            'device_identifiers' => $deviceIdentifiers,
        ];

        return $this->httpPostJson('shakearound/device/group/adddevice', $params);
    }

    /**
     * Remove one or more devices from a device group.
     *
     * @param int   $groupId
     * @param array $deviceIdentifiers
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function removeDevice($groupId, array $deviceIdentifiers)
    {
        $params = [
            'group_id' => intval($groupId),
            'device_identifiers' => $deviceIdentifiers,
        ];

        return $this->httpPostJson('shakearound/device/group/deletedevice', $params);
    }
}
