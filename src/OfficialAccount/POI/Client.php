<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\POI;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * Get POI supported categories.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function categories()
    {
        return $this->httpGet('cgi-bin/poi/getwxcategory');
    }

    /**
     * Get POI by ID.
     *
     * @param int $poiId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function get(int $poiId)
    {
        return $this->httpPostJson('cgi-bin/poi/getpoi', ['poi_id' => $poiId]);
    }

    /**
     * List POI.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function list(int $offset = 0, int $limit = 10)
    {
        $params = [
            'begin' => $offset,
            'limit' => $limit,
        ];

        return $this->httpPostJson('cgi-bin/poi/getpoilist', $params);
    }

    /**
     * Create a POI.
     *
     * @param array $baseInfo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function create(array $baseInfo)
    {
        $params = [
            'business' => [
                'base_info' => $baseInfo,
            ],
        ];

        return $this->httpPostJson('cgi-bin/poi/addpoi', $params);
    }

    /**
     * @param array $databaseInfo
     *
     * @return int
     */
    public function createAndGetId(array $databaseInfo)
    {
        return $this->create($databaseInfo)['poi_id'];
    }

    /**
     * Update a POI.
     *
     * @param int   $poiId
     * @param array $baseInfo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function update(int $poiId, array $baseInfo)
    {
        $params = [
            'business' => [
                'base_info' => array_merge($baseInfo, ['poi_id' => $poiId]),
            ],
        ];

        return $this->httpPostJson('cgi-bin/poi/updatepoi', $params);
    }

    /**
     * Delete a POI.
     *
     * @param int $poiId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete(int $poiId)
    {
        return $this->httpPostJson('cgi-bin/poi/delpoi', ['poi_id' => $poiId]);
    }
}
