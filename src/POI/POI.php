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
 * POI.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\POI;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class POI.
 */
class POI extends AbstractAPI
{
    const API_CREATE = 'http://api.weixin.qq.com/cgi-bin/poi/addpoi';
    const API_GET = 'http://api.weixin.qq.com/cgi-bin/poi/getpoi';
    const API_LIST = 'http://api.weixin.qq.com/cgi-bin/poi/getpoilist';
    const API_UPDATE = 'http://api.weixin.qq.com/cgi-bin/poi/updatepoi';
    const API_DELETE = 'http://api.weixin.qq.com/cgi-bin/poi/delpoi';

    /**
     * Get POI by ID.
     *
     * @param int $poiId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function get($poiId)
    {
        return $this->parseJSON('json', [self::API_GET, ['poi_id' => $poiId]]);
    }

    /**
     * List POI.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function lists($offset = 0, $limit = 10)
    {
        $params = [
                   'begin' => $offset,
                   'limit' => $limit,
                  ];

        return $this->parseJSON('json', [self::API_LIST, $params]);
    }

    /**
     * Create a POI.
     *
     * @param array $data
     *
     * @return bool
     */
    public function create(array $data)
    {
        $params = [
                   'business' => ['base_info' => $data],
                  ];

        return $this->parseJSON('json', [self::API_CREATE, $params]);
    }

    /**
     * Update a POI.
     *
     * @param int   $poiId
     * @param array $data
     *
     * @return bool
     */
    public function update($poiId, array $data)
    {
        $data = array_merge($data, ['poi_id' => $poiId]);

        $params = [
                   'business' => ['base_info' => $data],
                  ];

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    /**
     * Delete a POI.
     *
     * @param int $poiId
     *
     * @return bool
     */
    public function delete($poiId)
    {
        $params = ['poi_id' => $poiId];

        return $this->parseJSON('json', [self::API_DELETE, $params]);
    }
}
