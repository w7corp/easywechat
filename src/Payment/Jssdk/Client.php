<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Jssdk;

use EasyWeChat\BasicService\Jssdk\Client as JssdkClient;
use EasyWeChat\Kernel\Support;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends JssdkClient
{
    /**
     * [WeixinJSBridge] Generate js config for payment.
     *
     * <pre>
     * WeixinJSBridge.invoke(
     *  'getBrandWCPayRequest',
     *  ...
     * );
     * </pre>
     *
     * @param string $prepayId
     * @param bool   $json
     *
     * @return string|array
     */
    public function bridgeConfig(string $prepayId, bool $json = true)
    {
        $params = [
            'appId' => $this->app['config']->sub_appid ?: $this->app['config']->app_id,
            'timeStamp' => strval(time()),
            'nonceStr' => uniqid(),
            'package' => "prepay_id=$prepayId",
            'signType' => 'MD5',
        ];

        $params['paySign'] = Support\generate_sign($params, $this->app['config']->key, 'md5');

        return $json ? json_encode($params) : $params;
    }

    /**
     * [JSSDK] Generate js config for payment.
     *
     * <pre>
     * wx.chooseWXPay({...});
     * </pre>
     *
     * @param string $prepayId
     *
     * @return array
     */
    public function sdkConfig(string $prepayId): array
    {
        $config = $this->bridgeConfig($prepayId, false);

        $config['timestamp'] = $config['timeStamp'];
        unset($config['timeStamp']);

        return $config;
    }

    /**
     * Generate app payment parameters.
     *
     * @param string $prepayId
     *
     * @return array
     */
    public function appConfig(string $prepayId): array
    {
        $params = [
            'appid' => $this->app['config']->app_id,
            'partnerid' => $this->app['config']->mch_id,
            'prepayid' => $prepayId,
            'noncestr' => uniqid(),
            'timestamp' => time(),
            'package' => 'Sign=WXPay',
        ];

        $params['sign'] = Support\generate_sign($params, $this->app['config']->key);

        return $params;
    }

    /**
     * Generate js config for share user address.
     *
     * @param string $accessToken
     * @param bool                                            $json
     *
     * @return string|array
     */
    public function shareAddressConfig(string $accessToken, bool $json = true)
    {
        $params = [
            'appId' => $this->app['config']->app_id,
            'scope' => 'jsapi_address',
            'timeStamp' => strval(time()),
            'nonceStr' => uniqid(),
            'signType' => 'SHA1',
        ];

        $signParams = [
            'appid' => $params['appId'],
            'url' => $this->getUrl(),
            'timestamp' => $params['timeStamp'],
            'noncestr' => $params['nonceStr'],
            'accesstoken' => strval($accessToken),
        ];

        ksort($signParams);

        $params['addrSign'] = sha1(urldecode(http_build_query($signParams)));

        return $json ? json_encode($params) : $params;
    }

    /**
     * Generate js config for contract of mini program.
     *
     * @param array $params
     *
     * @return array
     */
    public function contractConfig(array $params): array
    {
        $params['appid'] = $this->app['config']->app_id;
        $params['timestamp'] = time();

        $params['sign'] = Support\generate_sign($params, $this->app['config']->key);

        return $params;
    }
}
