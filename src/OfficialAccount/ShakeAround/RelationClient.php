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
 * Class RelationClient.
 *
 * @author allen05ren <allen05ren@outlook.com>
 */
class RelationClient extends BaseClient
{
    /**
     * Bind pages for device.
     *
     * @param array $deviceIdentifier
     * @param array $pageIds
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function bindPages(array $deviceIdentifier, array $pageIds)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'page_ids' => $pageIds,
        ];

        return $this->httpPostJson('shakearound/device/bindpage', $params);
    }

    /**
     * Get pageIds by deviceId.
     *
     * @param array $deviceIdentifier
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection
     */
    public function listByDeviceId(array $deviceIdentifier)
    {
        $params = [
            'type' => 1,
            'device_identifier' => $deviceIdentifier,
        ];

        return $this->httpPostJson('shakearound/relation/search', $params);
    }

    /**
     * Get devices by pageId.
     *
     * @param int $pageId
     * @param int $begin
     * @param int $count
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function listByPageId(int $pageId, int $begin, int $count)
    {
        $params = [
            'type' => 2,
            'page_id' => $pageId,
            'begin' => $begin,
            'count' => $count,
        ];

        return $this->httpPostJson('shakearound/relation/search', $params);
    }
}
