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
     * @param string $cardId
     * @param string $encryptCode
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
        return $this->httpPostJson('cgi-bin/kf/account/list');
    }
}
