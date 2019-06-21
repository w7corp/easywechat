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
 * Class ContactWayClient.
 *
 * @author milkmeowo <milkmeowo@gmail.com>
 */
class ContactWayClient extends BaseClient
{
    /**
     * 配置客户联系「联系我」方式.
     *
     * @param int   $type
     * @param int   $scene
     * @param array $config
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function create(int $type, int $scene, array $config = [])
    {
        $params = array_merge([
            'type' => $type,
            'scene' => $scene,
        ], $config);

        return $this->httpPostJson('cgi-bin/externalcontact/add_contact_way', $params);
    }

    /**
     * 获取企业已配置的「联系我」方式.
     *
     * @param $configId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function get(string $configId)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/get_contact_way', [
            'config_id' => $configId,
        ]);
    }

    /**
     * 更新企业已配置的「联系我」方式.
     *
     * @param $configId
     * @param $config
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function update(string $configId, array $config = [])
    {
        $params = array_merge([
            'config_id' => $configId,
        ], $config);

        return $this->httpPostJson('cgi-bin/externalcontact/update_contact_way', $params);
    }

    /**
     * 删除企业已配置的「联系我」方式.
     *
     * @param $configId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function delete(string $configId)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/del_contact_way', [
            'config_id' => $configId,
        ]);
    }
}
