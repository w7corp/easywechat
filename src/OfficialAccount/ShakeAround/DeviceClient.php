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
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class DeviceClient.
 *
 * @author allen05ren <allen05ren@outlook.com>
 */
class DeviceClient extends BaseClient
{
    /**
     * Apply device ids.
     *
     * @param int    $quantity
     * @param string $reason
     * @param string $comment
     * @param int    $poiId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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

        return $this->httpPostJson('shakearound/device/applyid', $params);
    }

    /**
     * Get audit status.
     *
     * @param int $applyId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function getStatus($applyId)
    {
        $params = [
            'apply_id' => intval($applyId),
        ];

        return $this->httpPostJson('shakearound/device/applystatus', $params);
    }

    /**
     * Update a device comment.
     *
     * @param array  $deviceIdentifier
     * @param string $comment
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function update(array $deviceIdentifier, $comment)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'comment' => $comment,
        ];

        return $this->httpPostJson('shakearound/device/update', $params);
    }

    /**
     * Bind location for device.
     *
     * @param array  $deviceIdentifier
     * @param int    $poiId
     * @param int    $type
     * @param string $poiAppId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws InvalidArgumentException
     */
    public function bindLocation(array $deviceIdentifier, $poiId, $type = 1, $poiAppId = null)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'poi_id' => intval($poiId),
        ];

        if ($type === 2) {
            if (is_null($poiAppId)) {
                throw new InvalidArgumentException('If value of argument #3 is 2, argument #4 is required.');
            }
            $params['type'] = 2;
            $params['poi_appid'] = $poiAppId;
        }

        return $this->httpPostJson('shakearound/device/bindlocation', $params);
    }

    /**
     * Fetch batch of devices by deviceIds.
     *
     * @param array $deviceIdentifiers
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function getByIds(array $deviceIdentifiers)
    {
        $params = [
            'type' => 1,
            'device_identifiers' => $deviceIdentifiers,
        ];

        return $this->search($params);
    }

    /**
     * Pagination to get batch of devices.
     *
     * @param int $lastSeen
     * @param int $count
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function paginate($lastSeen, $count)
    {
        $params = [
            'type' => 2,
            'last_seen' => intval($lastSeen),
            'count' => intval($count),
        ];

        return $this->search($params);
    }

    /**
     * Fetch batch of devices by applyId.
     *
     * @param int $applyId
     * @param int $lastSeen
     * @param int $count
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function getByApplyId($applyId, $lastSeen, $count)
    {
        $params = [
            'type' => 3,
            'apply_id' => intval($applyId),
            'last_seen' => intval($lastSeen),
            'count' => intval($count),
        ];

        return $this->search($params);
    }

    /**
     * Fetch batch of devices.
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    protected function search($params)
    {
        return $this->httpPostJson('shakearound/device/search', $params);
    }
}
