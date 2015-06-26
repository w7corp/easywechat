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

use EasyWeChat\Support\Arr;
use EasyWeChat\Support\Collection;

/**
 * 门店.
 */
class Store
{
    /**
     * Http对象
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
     * constructor.
     *
     * <pre>
     * $config:
     *
     * array(
     *  'app_id' => YOUR_APPID,  // string mandatory;
     *  'secret' => YOUR_SECRET, // string mandatory;
     * )
     * </pre>
     *
     * @param array $config configuration array
     */
    public function __construct(array $config)
    {
        $this->http = new Http(new AccessToken($config));
    }

    /**
     * 获取指定门店信息.
     *
     * @param int $storeId
     *
     * @return EasyWeChat\Support\Collection
     */
    public function get($storeId)
    {
        $params = ['poi_id' => $storeId];

        $response = $this->http->jsonPost(self::API_GET, $params);

        return new Collection(Arr::get($response, 'business.base_info'));
    }

    /**
     * 获取用户列表.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return EasyWeChat\Support\Collection
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
     * 创建门店.
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
     * 更新门店.
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
     * 删除门店.
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
}
