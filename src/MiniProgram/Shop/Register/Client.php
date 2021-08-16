<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\Shop\Register;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author her-cat <hxhsoft@foxmail.com>
 */
class Client extends BaseClient
{
    /**
     * 接入申请
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function apply()
    {
        return $this->httpPostJson('shop/register/apply');
    }

    /**
     * 获取接入状态
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function check()
    {
        return $this->httpPostJson('shop/register/check');
    }

    /**
     * 完成接入任务
     *
     * @param int $accessInfoItem
     *            6:完成spu接口，7:完成订单接口，8:完成物流接口，9:完成售后接口，10:测试完成，11:发版完成
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function finishAccessInfo(int $accessInfoItem)
    {
        return $this->httpPostJson('shop/register/finish_access_info', [
            'access_info_item' => $accessInfoItem
        ]);
    }

    /**
     * 场景接入申请
     *
     * @param int $sceneGroupId 1:视频号、公众号场景

     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function applyScene(int $sceneGroupId = 1)
    {
        return $this->httpPostJson('shop/register/apply_scene', [
            'scene_group_id' => $sceneGroupId
        ]);
    }
}
