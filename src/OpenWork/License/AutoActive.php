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
 * License Client
 *
 * @author keller31 <xiaowei.vip@gmail.com>
 */
class AutoActive extends BaseClient
{
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app, $app['provider_access_token']);
    }

    /**
     * 设置企业的许可自动激活状态
     * 服务商可以调用该接口设置授权企业的许可自动激活状态。设置为自动激活后，对应授权企业的员工使用服务商应用时，接口许可表现为自动激活。
     *
     * @link https://developer.work.weixin.qq.com/document/path/95873
     *
     * @param string $corpid    企业ID
     * @param integer $status   许可自动激活状态。0：关闭，1：打开
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setStatus(string $corpid, int $status)
    {
        return $this->httpPostJson('cgi-bin/license/set_auto_active_status', [
            'corpid' => $corpid,
            'auto_active_status' => $status
        ]);
    }

    /**
     * 查询企业的许可自动激活状态
     * 服务商可以调用该接口查询授权企业的许可自动激活状态。
     *
     * @link https://developer.work.weixin.qq.com/document/path/95874
     *
     * @param string $corpid    企业ID
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStatus(string $corpid)
    {
        return $this->httpPostJson('cgi-bin/license/get_auto_active_status', [
            'corpid' => $corpid
        ]);
    }
}
