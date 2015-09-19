<?php

/**
 * @author windqyoung <windqyoung@qq.com>
 * @link https://github.com/windqyoung/
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\SignGenerator;
use Overtrue\Wechat\Utils\JSON;

/**
 * 微信收货地址共享接口, 参数计算
 *
 * 微信文档参考 {@link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_8}
 *
 * js调用收货地址接口后, 返回信息如下:
 *
 * 返回值	说明
 * err_msg	edit_address:ok获取编辑收货地址成功
 *          edit_address:fail获取编辑收货地址失败
 * userName	收货人姓名
 * telNumber	收货人电话
 * addressPostalCode	邮编
 * proviceFirstStageName	国标收货地址第一级地址
 * addressCitySecondStageName	国标收货地址第二级地址
 * addressCountiesThirdStageName	国标收货地址第三级地址
 * addressDetailInfo	详细收货地址信息
 * nationalCode	收货地址国家码
 */
class JsAddress
{
    /**
     * 网页授权中的access_token
     * 如果已获取此数据, 设置了以后, 直接计算签名
     * 否则需要跳转到微信服务器进行授权
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $appSecret;

    /**
     * @var string
     */
    private $timestamp;

    /**
     * 随机字符串
     * @var string
     */
    private $nonce;

    /**
     * 如无设置, 获取当前url.
     * 微信文档中说明: 如果是跳转到微信服务器授权返回来的, 当前url包括code和state
     * @var string
     */
    private $url;


    /**
     * @var array
     */
    private $accessPermission;


    /**
     * @var Input
     */
    private $input;


    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;

        $this->input = new Input();

        $this->refreshRandom();
    }

    public function refreshRandom()
    {
        $this->timestamp = (string) time();
        $this->nonce = uniqid('rnd_');
    }

    /**
     * Js中获取微信地址信息的参数
     */
    public function getConfig($asJson = true)
    {
        $config = [
            'appId' => $this->appId,
            'scope' => 'jsapi_address',
            'signType' => 'SHA1',
            'timeStamp' => $this->timestamp,
            'nonceStr' => $this->nonce,
            'addrSign' => $this->calcAddrSign(),
        ];

        return $asJson ? JSON::encode($config) : $config;
    }


    private function calcAddrSign()
    {
        $params = [
            // 此参数为获取网页授权时从微信服务器取得的access_token, 也可直接跳转到微信服务器获取
            'accesstoken'   => $this->getAccessTokenRedirectIfNecessary(),
            'appid'         => $this->appId,
            'noncestr'      => $this->nonce,
            'timestamp'     => $this->timestamp,
            'url'           => $this->url ?: Url::current(),
        ];

        $gen = new SignGenerator($params);
        $gen->setHashType('sha1');

        return $gen->getResult();
    }

    private function getAccessTokenRedirectIfNecessary()
    {
        // 如果已设置, 直接使用
        if ($this->accessToken)
        {
            return $this->accessToken;
        }

        // 否则跳转到微信服务器授权
        if ($this->shouldRedirect())
        {
            $this->redirect();
        }

        // 通过返回的$_GET['code']获取access_token
        $this->accessToken = $this->fetchAccessTokenByCode();
    }

    /**
     * 如果已拿到网页授权access_token, 设置即可.
     * @param string $accessToken
     * @return JsAddress
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function shouldRedirect()
    {
        return !$this->input->has('state') && !$this->input->has('code');
    }

    private function redirect()
    {
        $auth = new Auth($this->appId, $this->appSecret);

        $auth->redirect($this->url, 'jsapi_address');
    }

    public function redirectUrl()
    {
        $auth = new Auth($this->appId, $this->appSecret);

        return $auth->url($this->url, 'jsapi_address');
    }

    private function fetchAccessTokenByCode()
    {
        $auth = new Auth($this->appId, $this->appSecret);

        $this->accessPermission = $ap = $auth->getAccessPermission($this->input->get('code'));

        return $this->accessToken = $ap['access_token'];
    }
}











