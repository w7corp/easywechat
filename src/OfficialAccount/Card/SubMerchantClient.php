<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Card;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Support\Arr;

/**
 * Class SubMerchantClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class SubMerchantClient extends BaseClient
{
    /**
     * 添加子商户.
     *
     * @param array $info
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $info = [])
    {
        $params = [
            'info' => Arr::only($info, [
                'brand_name',
                'logo_url',
                'protocol',
                'end_time',
                'primary_category_id',
                'secondary_category_id',
                'agreement_media_id',
                'operator_media_id',
                'app_id',
            ]),
        ];

        return $this->httpPostJson('card/submerchant/submit', $params);
    }

    /**
     * 更新子商户.
     *
     * @param int   $merchantId
     * @param array $info
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(int $merchantId, array $info = [])
    {
        $params = [
            'info' => array_merge(
                ['merchant_id' => $merchantId],
                Arr::only($info, [
                    'brand_name',
                    'logo_url',
                    'protocol',
                    'end_time',
                    'primary_category_id',
                    'secondary_category_id',
                    'agreement_media_id',
                    'operator_media_id',
                    'app_id',
                ])
            ),
        ];

        return $this->httpPostJson('card/submerchant/update', $params);
    }

    /**
     * 获取子商户信息.
     *
     * @param int $merchantId
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(int $merchantId)
    {
        return $this->httpPostJson('card/submerchant/get', ['merchant_id' => $merchantId]);
    }

    /**
     * 批量获取子商户信息.
     *
     * @param int    $beginId
     * @param int    $limit
     * @param string $status
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(int $beginId = 0, int $limit = 50, string $status = 'CHECKING')
    {
        $params = [
            'begin_id' => $beginId,
            'limit' => $limit,
            'status' => $status,
        ];

        return $this->httpPostJson('card/submerchant/batchget', $params);
    }
}
