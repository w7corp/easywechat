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
 * Class DeviceClient.
 *
 * @author allen05ren <allen05ren@outlook.com>
 */
class DeviceClient extends BaseClient
{
    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function apply(array $data)
    {
        return $this->httpPostJson('shakearound/device/applyid', $data);
    }

    /**
     * Get audit status.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function status(int $applyId)
    {
        $params = [
            'apply_id' => $applyId,
        ];

        return $this->httpPostJson('shakearound/device/applystatus', $params);
    }

    /**
     * Update a device comment.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(array $deviceIdentifier, string $comment)
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function bindPoi(array $deviceIdentifier, int $poiId)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'poi_id' => $poiId,
        ];

        return $this->httpPostJson('shakearound/device/bindlocation', $params);
    }

    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function bindThirdPoi(array $deviceIdentifier, int $poiId, string $appId)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'poi_id' => $poiId,
            'type' => 2,
            'poi_appid' => $appId,
        ];

        return $this->httpPostJson('shakearound/device/bindlocation', $params);
    }

    /**
     * Fetch batch of devices by deviceIds.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function listByIds(array $deviceIdentifiers)
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function list(int $lastId, int $count)
    {
        $params = [
            'type' => 2,
            'last_seen' => $lastId,
            'count' => $count,
        ];

        return $this->search($params);
    }

    /**
     * Fetch batch of devices by applyId.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function listByApplyId(int $applyId, int $lastId, int $count)
    {
        $params = [
            'type' => 3,
            'apply_id' => $applyId,
            'last_seen' => $lastId,
            'count' => $count,
        ];

        return $this->search($params);
    }

    /**
     * Fetch batch of devices.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search(array $params)
    {
        return $this->httpPostJson('shakearound/device/search', $params);
    }
}
