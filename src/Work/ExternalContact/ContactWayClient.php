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
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @param string $configId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @param string $configId
     * @param array  $config
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

        return $this->httpPostJson('cgi-bin/externalcontact/update_contact_way', $params);
    }

    /**
     * 删除企业已配置的「联系我」方式.
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
        return $this->httpPostJson('cgi-bin/externalcontact/del_contact_way', [
            'config_id' => $configId,
        ]);
    }

    /**
     * 获取企业已配置的「联系我」列表，注意，该接口仅可获取2021年7月10日以后创建的「联系我」
     *
     * @param string $cursor 分页查询使用的游标，为上次请求返回的 next_cursor
     * @param int $limit 每次查询的分页大小，默认为100条，最多支持1000条
     * @param int|null $startTime 「联系我」创建起始时间戳, 不传默认为90天前
     * @param int|null $endTime 「联系我」创建结束时间戳, 不传默认为当前时间
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(string $cursor = '', int $limit = 100, int $startTime = null, int $endTime = null)
    {
        $data = [
            'cursor' => $cursor,
            'limit' => $limit,
        ];
        if ($startTime) {
            $data['start_time'] = $startTime;
        }
        if ($endTime) {
            $data['end_time'] = $endTime;
        }
        return $this->httpPostJson('cgi-bin/externalcontact/list_contact_way', $data);
    }
}
