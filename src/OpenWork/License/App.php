<?php
/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\License;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * License App Client
 *
 * @author keller31 <xiaowei.vip@gmail.com>
 */
class App extends BaseClient
{
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app, $app['provider_access_token']);
    }

    /**
     * 获取应用的接口许可状态
     * 服务商可获取某个授权企业的应用接口许可试用期，免费试用期为企业首次安装应用后的90天。
     *
     * @link https://developer.work.weixin.qq.com/document/path/95844
     *
     * @param string $corpid    企业id
     * @param string $suite_id  套件id
     * @param string $appid     旧的多应用套件中的应用id，新开发者请忽略
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $corpid, string $suite_id, string $appid = '')
    {
        return $this->httpPostJson('cgi-bin/license/get_app_license_info', [
            'corpid' => $corpid,
            'suite_id' => $suite_id,
            'appid' => $appid
        ]);
    }
}
