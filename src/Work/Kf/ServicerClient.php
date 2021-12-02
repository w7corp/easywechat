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
 * Class ServicerClient.
 *
 * @package EasyWeChat\Work\Kf
 *
 * @author 读心印 <aa24615@qq.com>
 */
class ServicerClient extends BaseClient
{
    /**
     * 添加接待人员.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94646
     *
     * @param string $openKfId
     * @param array $userIds
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(string $openKfId, array $userIds)
    {
        $params = [
            'open_kfid' => $openKfId,
            'userid_list' => $userIds
        ];

        return $this->httpPostJson('cgi-bin/kf/servicer/add', $params);
    }

    /**
     * 删除接待人员.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94647
     *
     * @param string $openKfId
     * @param array $userIds
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function del(string $openKfId, array $userIds)
    {
        $params = [
            'open_kfid' => $openKfId,
            'userid_list' => $userIds
        ];

        return $this->httpPostJson('cgi-bin/kf/servicer/del', $params);
    }

    /**
     * 获取接待人员列表.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94645
     *
     * @param string $openKfId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(string $openKfId)
    {
        $params = [
            'open_kfid' => $openKfId
        ];

        return $this->httpGet('cgi-bin/kf/servicer/list', $params);
    }
}
