<?php
namespace Overtrue\Wechat\Services;

use Overtrue\Wechat\Wechat;

/**
 * 卡券
 */
class Card
{
    const API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card';

    /**
     * 获取jsticket
     *
     * @return string
     */
    public function getTicket()
    {
        $key = 'overtrue.wechat.cardapi_ticket';
        $cache = Wechat::service('cache');

        return $cache->get($key, function($key) use ($cache) {
            $result = Wechat::request('GET', self::API_TICKET);

            $cache->set($key, $result['access_token'], $result['expires_in']);

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