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
 * Device.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue
 * @see       http://overtrue.me
 */

namespace EasyWeChat\ShakeAround;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;

/**
 * Class Device.
 */
class Device extends AbstractAPI
{
    const API_DEVICE_APPLYID = 'https://api.weixin.qq.com/shakearound/device/applyid';
    const API_DEVICE_APPLYSTATUS = 'https://api.weixin.qq.com/shakearound/device/applystatus';
    const API_DEVICE_UPDATE = 'https://api.weixin.qq.com/shakearound/device/update';
    const API_DEVICE_BINDLOCATION = 'https://api.weixin.qq.com/shakearound/device/bindlocation';
    const API_DEVICE_SEARCH = 'https://api.weixin.qq.com/shakearound/device/search';

    /**
     * Apply device ids.
     *
     * @param int    $quantity
     * @param string $reason
     * @param string $comment
     * @param int    $poi_id
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function apply($quantity, $reason, $comment = '', $poi_id = null)
    {
        $params = [
            'quantity' => intval($quantity),
            'apply_reason' => $reason,
        ];

        if (!empty($comment)) {
            $params['comment'] = $comment;
        }

        if (!is_null($poi_id)) {
            $params['poi_id'] = intval($poi_id);
        }

        return $this->parseJSON('json', [self::API_DEVICE_APPLYID, $params]);
    }

    /**
     * Get audit status.
     *
     * @param int $apply_id
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getStatus($apply_id)
    {
        $params = [
            'apply_id' => intval($apply_id),
        ];

        return $this->parseJSON('json', [self::API_DEVICE_APPLYSTATUS, $params]);
    }

    /**
     * Update a device comment.
     *
     * @param array  $device_identifier
     * @param string $comment
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function update(array $device_identifier, $comment)
    {
        $params = [
            'device_identifier' => $device_identifier,
            'comment' => $comment,
        ];

        return $this->parseJSON('json', [self::API_DEVICE_UPDATE, $params]);
    }

    /**
     * Bind location for device.
     *
     * @param array  $device_identifier
     * @param int    $poi_id
     * @param int    $type
     * @param string $poi_appid
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws InvalidArgumentException
     */
    public function bindLocation(array $device_identifier, $poi_id, $type = 1, $poi_appid = null)
    {
        $params = [
            'device_identifier' => $device_identifier,
            'poi_id' => intval($poi_id),
        ];

        if ($type === 2) {
            if (is_null($poi_appid)) {
                throw new InvalidArgumentException('If value of argument #3 is 2, argument #4 is required.');
            }
            $params['type'] = 2;
            $params['poi_appid'] = $poi_appid;
        }

        return $this->parseJSON('json', [self::API_DEVICE_BINDLOCATION, $params]);
    }

    /**
     * Fetch batch of devices by device_ids.
     *
     * @param array $device_identifiers
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function fetchByIds(array $device_identifiers)
    {
        $params = [
            'type' => 1,
            'device_identifiers' => $device_identifiers,
        ];

        return $this->fetch($params);
    }

    /**
     * Pagination to fetch batch of devices.
     *
     * @param int $last_seen
     * @param int $count
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function pagination($last_seen, $count)
    {
        $params = [
            'type' => 2,
            'last_seen' => intval($last_seen),
            'count' => intval($count),
        ];

        return $this->fetch($params);
    }

    /**
     * Fetch batch of devices by apply_id.
     *
     * @param int $apply_id
     * @param int $last_seen
     * @param int $count
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function fetchByApplyId($apply_id, $last_seen, $count)
    {
        $params = [
            'type' => 3,
            'apply_id' => intval($apply_id),
            'last_seen' => intval($last_seen),
            'count' => intval($count),
        ];

        return $this->fetch($params);
    }

    /**
     * Fetch batch of devices.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    private function fetch($params)
    {
        return $this->parseJSON('json', [self::API_DEVICE_SEARCH, $params]);
    }
}
