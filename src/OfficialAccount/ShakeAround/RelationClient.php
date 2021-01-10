<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\ShakeAround;

use EasyWeChat\Kernel\BaseClient;

class RelationClient extends BaseClient
{
    /**
     * Bind pages for device.
     *
     * @param array $deviceIdentifier
     * @param array $pageIds
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
