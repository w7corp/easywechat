<?php
namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Arr;

/**
 * 门店
 */
class Store
{
    /**
     * 请求的headers
     *
     * @var array
     */
    protected $headers = array('content-type:application/json');

    const API_CREATE    = 'http://api.weixin.qq.com/cgi-bin/poi/addpoi';
    const API_GET       = 'http://api.weixin.qq.com/cgi-bin/poi/getpoi';
    const API_LIST      = 'http://api.weixin.qq.com/cgi-bin/poi/getpoilist';
    const API_UPDATE    = 'http://api.weixin.qq.com/cgi-bin/poi/updatepoi';
    const API_DELETE    = 'http://api.weixin.qq.com/cgi-bin/poi/delpoi';


    /**
     * 获取指定门店信息
     *
     * @param int $storeId
     *
     * @return Overtrue\Wechat\Utils\Bag
     */
    public function get($storeId)
    {
        $params = array(
                   'poi_id' => $storeId,
                  );

        $response = Wechat::request('POST', self::API_GET, $params);

        return new Bag(Arr::get($response, 'business.base_info'));
    }

    /**
     * 获取用户列表
     *
     * @param int $offset
     * @param int $limit
     *
     * @return Overtrue\Wechat\Utils\Bag
     */
    public function all($offset = 0, $limit = 10)
    {
        $params = array(
                   'begin' => $offset,
                   'limit' => $limit,
                  );

        $stores = Wechat::request('POST', self::API_LIST, $params);

        return Arr::fetch($stores['business_list'], 'base_info');
    }

    /**
     * 创建门店
     *
     * @param array $data
     *
     * @return boolean
     */
    public function create(array $data)
    {
        $params = array(
                   'business' => array(
                                  'base_info' => $data,
                                 ),
                  );

        return Wechat::request('POST', self::API_CREATE, $params);
    }

    /**
     * 更新门店
     *
     * @param int     $storeId
     * @param array   $data
     *
     * @return boolean
     */
    public function update($storeId, array $data)
    {
        $data = array_merge($data, array('poi_id' => $storeId));

        $params = array(
                   'business' => array(
                                  'base_info' => $data,
                                 ),
                  );

        return Wechat::request('POST', self::API_UPDATE, $params);
    }

    /**
     * 删除门店
     *
     * @param int $storeId
     *
     * @return boolean
     */
    public function delete($storeId)
    {
        $params = array(
                   'poi_id' => $storeId,
                  );

        return Wechat::request('POST', self::API_DELETE, $params);
    }
}