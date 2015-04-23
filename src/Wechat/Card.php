<?php
namespace Overtrue\Wechat;

/**
 * 卡券
 */
class Card
{
    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret
     *
     * @var string
     */
    protected $appSecret;

    /**
     * Cache对象
     *
     * @var Cache
     */
    protected $cache;

    const API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card';


    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->cache     = new Cache($appId);
    }

    /**
     * 获取jsticket
     *
     * @return string
     */
    public function getTicket()
    {
        $key = 'overtrue.wechat.cardapi_ticket' . $this->appId;

        return $this->cache->get($key, function($key) {

            $http  = new Http(new AccessToken($this->appId, $this->appSecret));

            $result = $http->get(self::API_TICKET);

            $this->cache->set($key, $result['access_token'], $result['expires_in']);

            return $result['access_token'];
        });
    }

    /**
     * 生成签名
     *
     * @param string  $ticket
     * @param string  $nonce
     * @param int     $timestamp
     * @param string  $url
     *
     * @return string
     */
    public function getSignature($ticket, $appId, $locationId, $timestamp, $nonce, $cardId, $cardType)
    {
        $params = func_get_args();

        sort($params, SORT_STRING);

        return sha1(implode($params));
    }

    /**
     * 获取随机字符串
     *
     * @return string
     */
    public function getNonce()
    {
        return uniqid();
    }
}