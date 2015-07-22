<?php

/**
 * Store.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Store;

use EasyWeChat\Core\Http;
use EasyWeChat\Support\Arr;
use EasyWeChat\Support\Collection;

/**
 * Class Manager.
 */
class Manager
{
    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    const API_CREATE = 'http://api.weixin.qq.com/cgi-bin/poi/addpoi';
    const API_GET = 'http://api.weixin.qq.com/cgi-bin/poi/getpoi';
    const API_LIST = 'http://api.weixin.qq.com/cgi-bin/poi/getpoilist';
    const API_UPDATE = 'http://api.weixin.qq.com/cgi-bin/poi/updatepoi';
    const API_DELETE = 'http://api.weixin.qq.com/cgi-bin/poi/delpoi';

    /**
     * Constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http->setExpectedException('EasyWeChat\Store\StoreHttpException');
    }

    /**
     * Get store.
     *
     * @param int $storeId
     *
     * @return Collection
     */
    public function get($storeId)
    {
        $params = ['poi_id' => $storeId];

        $response = $this->http->jsonPost(self::API_GET, $params);

        return new Collection(Arr::get($response, 'business.base_info'));
    }

    /**
     * List stores.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return Collection
     */
    public function lists($offset = 0, $limit = 10)
    {
        $params = [
                   'begin' => $offset,
                   'limit' => $limit,
                  ];

        $stores = $this->http->jsonPost(self::API_LIST, $params);

        return Arr::fetch($stores['business_list'], 'base_info');
    }

    /**
     * Create store.
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

        return $this->http->jsonPost(self::API_CREATE, $params);
    }

    /**
     * Update a store.
     *
     * @param int   $storeId
     * @param array $data
     *
     * @return bool
     */
    public function update($storeId, array $data)
    {
        $data = array_merge($data, ['poi_id' => $storeId]);

        $params = [
                   'business' => ['base_info' => $data],
                  ];

        return $this->http->jsonPost(self::API_UPDATE, $params);
    }

    /**
     * Delete a store.
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function delete($storeId)
    {
        $params = ['poi_id' => $storeId];

        return $this->http->jsonPost(self::API_DELETE, $params);
    }
}//end class

