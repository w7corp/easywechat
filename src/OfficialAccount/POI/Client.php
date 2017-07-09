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
    public function getCategories()
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
    public function get($poiId)
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
    public function lists($offset = 0, $limit = 10)
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
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function create(array $data)
    {
        $params = [
            'business' => [
                'base_info' => $data,
            ],
        ];

        return $this->httpPostJson('cgi-bin/poi/addpoi', $params);
    }

    /**
     * @param array $data
     *
     * @return int
     */
    public function createAndGetId(array $data)
    {
        return $this->create($data)['poi_id'];
    }

    /**
     * Update a POI.
     *
     * @param int   $poiId
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function update($poiId, array $data)
    {
        $params = [
            'business' => [
                'base_info' => array_merge($data, ['poi_id' => $poiId]),
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
    public function delete($poiId)
    {
        return $this->httpPostJson('cgi-bin/poi/delpoi', ['poi_id' => $poiId]);
    }
}
