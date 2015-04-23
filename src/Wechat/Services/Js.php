<?php
namespace Overtrue\Wechat\Services;

use Overtrue\Wechat\Wechat;

/**
 * 微信 JSSDK
 */
class Js
{
    const API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket&type=jsapi';

    /**
     * 当前URL
     *
     * @var string
     */
    protected $url;


    /**
     * 获取JSSDK的配置数组
     *
     * @param array   $APIs
     * @param boolean $debug
     * @param boolean $json
     *
     * @return array
     */
    public function config(array $APIs, $debug = false, $json = false)
    {
        $signPackage = $this->getSignaturePackage();

        $config = array_merge(array('debug' => $debug), $signPackage, array('jsApiList' => $APIs));

        return $json ? json_encode($config) : $config;
    }

    /**
     * 获取jsticket
     *
     * @return string
     */
    public function getTicket()
    {
        $key = 'overtrue.wechat.jsapi_ticket';
        $cache = Wechat::service('cache');

        return $cache->get($key, function($key) use ($cache) {
            $result = Wechat::request('GET', self::API_TICKET);

            $cache->set($key, $result['access_token'], $result['expires_in']);

            return $result['access_token'];
        });
    }

    /**
     * 签名
     *
     * @param string $url
     * @param string $nonce
     * @param int    $timestamp
     *
     * @return array
     */
    public function getSignaturePackage($url = null, $nonce = null, $timestamp = null)
    {
        $url       = $url ? $url : $this->getUrl();
        $nonce     = $nonce ? $nonce : $this->getNonce();
        $timestamp = $timestamp ? $timestamp : time();
        $ticket    = $this->getTicket();

        $sign = array(
                 "appId"     => Wechat::option('appId'),
                 "nonceStr"  => $nonce,
                 "timestamp" => $timestamp,
                 "url"       => $url,
                 "signature" => $this->getSignature($ticket, $nonce, $timestamp, $url),
                );

        return $sign;
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
    public function getSignature($ticket, $nonce, $timestamp, $url)
    {
        return sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");
    }

    /**
     * 设置当前URL
     *
     * @param string $url
     *
     * @return Js
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * 获取当前URL
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->url) {
            return $this->url;
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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