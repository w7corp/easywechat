<?php
namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;

/**
 * 链接
 */
class Url
{
    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    const API_SHORT_URL = 'https://api.weixin.qq.com/cgi-bin/shorturl';


    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 转短链接
     *
     * @param string $url
     *
     * @return string
     */
    public function short($url)
    {
        $params = array(
                   'action'   => 'long2short',
                   'long_url' => $url,
                  );

        $response = $this->http->jsonPost(self::API_SHORT_URL, $params);

        return $response['short_url'];
    }
}