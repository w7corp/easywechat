<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\ExternalContact;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class GroupChatWayClient.
 *
 * @see https://developer.work.weixin.qq.com/document/path/92229
 *
 * @author SinyLi <yxlix1@163.com>
 */
class GroupChatWayClient extends BaseClient
{
    /**
     * 配置客户群进群方式
     *
     * @param array $params 创建参数
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $params)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/groupchat/add_join_way', $params);
    }

    /**
     * 获取客户群进群方式配置
     *
     * @param string $configId 联系方式的配置id
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $configId)
    {
        $params = [
            'config_id' => $configId
        ];
        return $this->httpPostJson('cgi-bin/externalcontact/groupchat/get_join_way', $params);
    }

    /**
     * 更新客户群进群方式配置，注意：使用覆盖的方式更新
     *
     * @param string $configId 企业联系方式的配置id
     * @param array $config 更新参数
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $configId, array $config = [])
    {
        $params = array_merge([
            'config_id' => $configId,
        ], $config);
        return $this->httpPostJson('cgi-bin/externalcontact/groupchat/update_join_way', $params);
    }

    /**
     * 删除客户群进群方式配置
     *
     * @param string $configId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $configId)
    {
        $params = [
            'config_id' => $configId
        ];
        return $this->httpPostJson('cgi-bin/externalcontact/groupchat/del_join_way', $params);
    }
}
