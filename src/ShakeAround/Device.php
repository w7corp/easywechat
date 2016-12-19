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
     * @param int    $poiId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function apply($quantity, $reason, $comment = '', $poiId = null)
    {
        $params = [
            'quantity' => intval($quantity),
            'apply_reason' => $reason,
        ];

        if (!empty($comment)) {
            $params['comment'] = $comment;
        }

        if (!is_null($poiId)) {
            $params['poi_id'] = intval($poiId);
        }

        return $this->parseJSON('json', [self::API_DEVICE_APPLYID, $params]);
    }

    /**
     * Get audit status.
     *
     * @param int $applyId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getStatus($applyId)
    {
        $params = [
            'apply_id' => intval($applyId),
        ];

        return $this->parseJSON('json', [self::API_DEVICE_APPLYSTATUS, $params]);
    }

    /**
     * Update a device comment.
     *
     * @param array  $deviceIdentifier
     * @param string $comment
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function update(array $deviceIdentifier, $comment)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'comment' => $comment,
        ];

        return $this->parseJSON('json', [self::API_DEVICE_UPDATE, $params]);
    }

    /**
     * Bind location for device.
     *
     * @param array  $deviceIdentifier
     * @param int    $poiId
     * @param int    $type
     * @param string $poiAppid
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws InvalidArgumentException
     */
    public function bindLocation(array $deviceIdentifier, $poiId, $type = 1, $poiAppid = null)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'poi_id' => intval($poiId),
        ];

        if ($type === 2) {
            if (is_null($poiAppid)) {
                throw new InvalidArgumentException('If value of argument #3 is 2, argument #4 is required.');
            }
            $params['type'] = 2;
            $params['poi_appid'] = $poiAppid;
        }

        return $this->parseJSON('json', [self::API_DEVICE_BINDLOCATION, $params]);
    }

    /**
     * Fetch batch of devices by deviceIds.
     *
     * @param array $deviceIdentifiers
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function fetchByIds(array $deviceIdentifiers)
    {
        $params = [
            'type' => 1,
            'device_identifiers' => $deviceIdentifiers,
        ];

        return $this->fetch($params);
    }

    /**
     * Pagination to fetch batch of devices.
     *
     * @param int $lastSeen
     * @param int $count
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function pagination($lastSeen, $count)
    {
        $params = [
            'type' => 2,
            'last_seen' => intval($lastSeen),
            'count' => intval($count),
        ];

        return $this->fetch($params);
    }

    /**
     * Fetch batch of devices by applyId.
     *
     * @param int $applyId
     * @param int $lastSeen
     * @param int $count
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function fetchByApplyId($applyId, $lastSeen, $count)
    {
        $params = [
            'type' => 3,
            'apply_id' => intval($applyId),
            'last_seen' => intval($lastSeen),
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
