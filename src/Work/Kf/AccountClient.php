<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Kf;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class AccountClient.
 *
 * @package EasyWeChat\Work\Kf
 *
 * @author 读心印 <aa24615@qq.com>
 */
class AccountClient extends BaseClient
{
    /**
     * 添加客服帐号.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94662
     *
     * @param string $name
     * @param string $mediaId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(string $name, string $mediaId)
    {
        $params = [
            'name' => $name,
            'media_id' => $mediaId,
        ];

        return $this->httpPostJson('cgi-bin/kf/account/add', $params);
    }

    /**
     * 修改客服帐号.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94664
     *
     * @param string $openKfId
     * @param string $name
     * @param string $mediaId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $openKfId, string $name, string $mediaId)
    {
        $params = [
            'open_kfid' => $openKfId,
            'name' => $name,
            'media_id' => $mediaId,
        ];

        return $this->httpPostJson('cgi-bin/kf/account/update', $params);
    }

    /**
     * 删除客服帐号.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94663
     *
     * @param string $openKfId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function del(string $openKfId)
    {
        $params = [
            'open_kfid' => $openKfId
        ];

        return $this->httpPostJson('cgi-bin/kf/account/del', $params);
    }

    /**
     * 获取客服帐号列表.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94661
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list()
    {
        return $this->httpGet('cgi-bin/kf/account/list');
    }

    /**
     * 获取客服帐号链接.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94665
     *
     * @param string $openKfId
     * @param string $scene
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccountLink(string $openKfId, string $scene)
    {
        $params = [
            'open_kfid' => $openKfId,
            'scene' => $scene
        ];

        return $this->httpPostJson('cgi-bin/kf/add_contact_way', $params);
    }
}
