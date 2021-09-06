<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\QrCode;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * QrCode Client
 *
 * 普通链接二维码
 *
 * @link https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/qrcode/qrcode.html
 * @link https://developers.weixin.qq.com/miniprogram/introduction/qrcode.html
 *
 * @author dysodeng <dysodengs@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 获取已设置的二维码规则
     *
     * @return array|Collection|object|ResponseInterface|string
     *
     * @throws InvalidConfigException
     * @throws GuzzleException
     */
    public function list()
    {
        return $this->httpPostJson('cgi-bin/wxopen/qrcodejumpget');
    }

    /**
     * 获取校验文件名称及内容
     *
     * @return array|Collection|object|ResponseInterface|string
     *
     * @throws GuzzleException
     * @throws InvalidConfigException
     */
    public function getVerifyFile()
    {
        return $this->httpPostJson('cgi-bin/wxopen/qrcodejumpdownload');
    }

    /**
     * 增加或修改二维码规则
     *
     * @param array $params
     *
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws InvalidConfigException
     */
    public function set(array $params)
    {
        return $this->httpPostJson('cgi-bin/wxopen/qrcodejumpadd', $params);
    }

    /**
     * 发布已设置的二维码规则
     *
     * @param string $prefix
     *
     * @return array|Collection|object|ResponseInterface|string
     *
     * @throws GuzzleException
     * @throws InvalidConfigException
     */
    public function publish(string $prefix)
    {
        $params = [
            'prefix' => $prefix
        ];
        return $this->httpPostJson('cgi-bin/wxopen/qrcodejumppublish', $params);
    }

    /**
     * 删除已设置的二维码规则
     *
     * @param string $prefix
     *
     * @return array|Collection|object|ResponseInterface|string
     *
     * @throws GuzzleException
     * @throws InvalidConfigException
     */
    public function delete(string $prefix)
    {
        $params = [
            'prefix' => $prefix
        ];
        return $this->httpPostJson('cgi-bin/wxopen/qrcodejumpdelete', $params);
    }

    /**
     * 将二维码长链接转成短链接
     *
     * @param string $long_url
     *
     * @return array|Collection|object|ResponseInterface|string
     *
     * @throws GuzzleException
     * @throws InvalidConfigException
     */
    public function shortUrl(string $long_url)
    {
        $params = [
            'long_url' => $long_url,
            'action' => 'long2short'
        ];
        return $this->httpPostJson('cgi-bin/shorturl', $params);
    }
}
