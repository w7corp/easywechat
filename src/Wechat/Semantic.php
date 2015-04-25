<?php
namespace Overtrue\Wechat;

/**
 * 语义理解
 */
class Semantic
{
    const API_SEARCH = 'https://api.weixin.qq.com/semantic/semproxy/search';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;


    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 创建分组
     *
     * @param string $name
     *
     * @return integer
     */
    public function query($keyword, $categories, array $other = array())
    {
        $params = array(
                   'query'    => $keyword,
                   'category' => join(',', (array) $categories),
                   'appid'    => $appId,
                  );

        return new Bag($this->http->jsonPost(self::API_CREATE, array_merge($params, $other)));
    }
}