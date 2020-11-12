<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Account;

use EasyWeChat\OpenPlatform\Authorizer\Aggregate\Account\Client as BaseClient;

/**
 * Class Client.
 *
 * @author ClouderSky <clouder.flow@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 获取账号基本信息.
     */
    public function getBasicInfo()
    {
        return $this->httpPostJson('cgi-bin/account/getaccountbasicinfo');
    }

    /**
     * 修改头像.
     *
     * @param string $mediaId 头像素材mediaId
     * @param string $left    剪裁框左上角x坐标（取值范围：[0, 1]）
     * @param string $top     剪裁框左上角y坐标（取值范围：[0, 1]）
     * @param string $right   剪裁框右下角x坐标（取值范围：[0, 1]）
     * @param string $bottom  剪裁框右下角y坐标（取值范围：[0, 1]）
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateAvatar(
        string $mediaId,
        $left = '0.0',
        $top = '0.0',
        $right = '1.0',
        $bottom = '1.0'
    ) {
        $params = [
            'head_img_media_id' => $mediaId,
            'x1' => \strval($left),
            'y1' => \strval($top),
            'x2' => \strval($right),
            'y2' => \strval($bottom),
        ];

        return $this->httpPostJson('cgi-bin/account/modifyheadimage', $params);
    }

    /**
     * 修改功能介绍.
     *
     * @param string $signature 功能介绍（简介）
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateSignature(string $signature)
    {
        $params = ['signature' => $signature];

        return $this->httpPostJson('cgi-bin/account/modifysignature', $params);
    }
}
