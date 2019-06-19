<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Crm;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class DimissionClient.
 *
 * @author milkmeowo <milkmeowo@gmail.com>
 */
class DimissionClient extends BaseClient
{
    /**
     * 获取离职成员的客户列表.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91563
     *
     * @param int $pageId
     * @param int $pageSize
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getUnassignedList(int $pageId = 0, int $pageSize = 1000)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/get_unassigned_list', [
            'page_id' => $pageId,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * 离职成员的外部联系人再分配.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91564
     *
     * @param string $externalUserId
     * @param string $handoverUserId
     * @param string $takeoverUserId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function transfer(string $externalUserId, string $handoverUserId, string $takeoverUserId)
    {
        $params = [
            'external_userid' => $externalUserId,
            'handover_userid' => $handoverUserId,
            'takeover_userid' => $takeoverUserId,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/transfer', $params);
    }
}
