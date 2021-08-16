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
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 获取配置了客户联系功能的成员列表.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91554
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function getFollowUsers()
    {
        return $this->httpGet('cgi-bin/externalcontact/get_follow_user_list');
    }

    /**
     * 获取外部联系人列表.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91555
     *
     * @param string $userId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function list(string $userId)
    {
        return $this->httpGet('cgi-bin/externalcontact/list', [
            'userid' => $userId,
        ]);
    }

    /**
     * 批量获取客户详情.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92994
     *
     * @param string $userId
     * @param string $cursor
     * @param integer $limit
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function batchGet(string $userId, string $cursor = '', int $limit = 100)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/batch/get_by_user', [
            'userid' => $userId,
            'cursor' => $cursor,
            'limit' => $limit,
        ]);
    }

    /**
     * 获取外部联系人详情.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91556
     *
     * @param string $externalUserId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $externalUserId)
    {
        return $this->httpGet('cgi-bin/externalcontact/get', [
            'external_userid' => $externalUserId,
        ]);
    }

    /**
     * 批量获取外部联系人详情.
     *
     * @see https://work.weixin.qq.com/api/doc/90001/90143/93010
     *
     * @param string $userId
     * @param string $cursor
     * @param int $limit
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function batchGetByUser(string $userId, string $cursor, int $limit)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/batch/get_by_user', [
            'userid' => $userId,
            'cursor' => $cursor,
            'limit' => $limit
        ]);
    }


    /**
     * 修改客户备注信息.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92115
     *
     * @param array $data
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function remark(array $data)
    {
        return $this->httpPostJson(
            'cgi-bin/externalcontact/remark',
            $data
        );
    }


    /**
     * 获取离职成员的客户列表.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92124
     *
     * @param int $pageId
     * @param int $pageSize
     * @param string $cursor
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUnassigned(int $pageId = 0, int $pageSize = 1000, ?string $cursor = null)
    {
        $params = [
            'page_id' => $pageId,
            'page_size' => $pageSize,
            'cursor' => $cursor,
        ];
        $writableParams = array_filter($params, function (string $key) use ($params) {
            return $params[$key] ?? null;
        }, ARRAY_FILTER_USE_KEY);
        return $this->httpPostJson('cgi-bin/externalcontact/get_unassigned_list', $writableParams);
    }

    /**
     * 离职成员的外部联系人再分配.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91564
     *
     * @param string $externalUserId
     * @param string $handoverUserId
     * @param string $takeoverUserId
     * @param string $transferSuccessMessage
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transfer(string $externalUserId, string $handoverUserId, string $takeoverUserId, string $transferSuccessMessage)
    {
        $params = [
            'external_userid' => $externalUserId,
            'handover_userid' => $handoverUserId,
            'takeover_userid' => $takeoverUserId,
            'transfer_success_msg' => $transferSuccessMessage
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/transfer', $params);
    }

    /**
     * 分配在职成员的客户.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92125
     *
     * @param array $externalUserId
     * @param string $handoverUserId
     * @param string $takeoverUserId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transferCustomer(array $externalUserId, string $handoverUserId, string $takeoverUserId, string $transferSuccessMessage)
    {
        $params = [
            'external_userid' => $externalUserId,
            'handover_userid' => $handoverUserId,
            'takeover_userid' => $takeoverUserId,
            'transfer_success_msg' => $transferSuccessMessage
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/transfer_customer', $params);
    }

    /**
     * 分配离职成员的客户.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/94081
     *
     * @param array $externalUserId
     * @param string $handoverUserId
     * @param string $takeoverUserId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function resignedTransferCustomer(array $externalUserId, string $handoverUserId, string $takeoverUserId)
    {
        $params = [
            'external_userid' => $externalUserId,
            'handover_userid' => $handoverUserId,
            'takeover_userid' => $takeoverUserId,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/resigned/transfer_customer', $params);
    }

    /**
     * 离职成员的群再分配.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92127
     *
     * @param array $chatIds
     * @param string $newOwner
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transferGroupChat(array $chatIds, string $newOwner)
    {
        $params = [
            'chat_id_list' => $chatIds,
            'new_owner' => $newOwner
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/groupchat/transfer', $params);
    }

    /**
     * 查询客户接替状态.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/94082
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @param string $handoverUserId
     * @param string $takeoverUserId
     * @param null|string $cursor
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transferResult(string $handoverUserId, string $takeoverUserId, ?string $cursor = null)
    {
        $params = [
            'handover_userid' => $handoverUserId,
            'takeover_userid' => $takeoverUserId,
            'cursor' => $cursor,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/resigned/transfer_result', $params);
    }

    /**
     * 查询客户接替结果.
     *
     * @see https://work.weixin.qq.com/api/doc/90001/90143/93009
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @param string $externalUserId
     * @param string $handoverUserId
     * @param string $takeoverUserId
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTransferResult(string $externalUserId, string $handoverUserId, string $takeoverUserId)
    {
        $params = [
            'external_userid' => $externalUserId,
            'handover_userid' => $handoverUserId,
            'takeover_userid' => $takeoverUserId,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/get_transfer_result', $params);
    }

    /**
     * 获取客户群列表.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92120
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function getGroupChats(array $params)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/groupchat/list', $params);
    }

    /**
     * 获取客户群详情.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92122
     *
     * @param string $chatId
     * @param int $needName
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function getGroupChat(string $chatId, int $needName = 0)
    {
        $params = [
            'chat_id' => $chatId,
            'need_name' => $needName,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/groupchat/get', $params);
    }

    /**
     * 获取企业标签库.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92117#获取企业标签库
     *
     * @param array $tagIds
     * @param array $groupIds
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function getCorpTags(array $tagIds = [], array $groupIds = [])
    {
        $params = [
            'tag_id' => $tagIds,
            'group_id' => $groupIds
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/get_corp_tag_list', $params);
    }


    /**
     * 添加企业客户标签.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92117#添加企业客户标签
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function addCorpTag(array $params)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/add_corp_tag', $params);
    }


    /**
     * 编辑企业客户标签.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92117#编辑企业客户标签
     *
     * @param string $id
     * @param string|null $name
     * @param int|null $order
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function updateCorpTag(string $id, ?string $name = null, ?int $order = null)
    {
        $params = [
            "id" => $id
        ];

        if (!\is_null($name)) {
            $params['name'] = $name;
        }

        if (!\is_null($order)) {
            $params['order'] = $order;
        }

        return $this->httpPostJson('cgi-bin/externalcontact/edit_corp_tag', $params);
    }


    /**
     * 删除企业客户标签.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92117#删除企业客户标签
     *
     * @param array $tagId
     * @param array $groupId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function deleteCorpTag(array $tagId, array $groupId)
    {
        $params = [
            "tag_id" => $tagId,
            "group_id" => $groupId,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/del_corp_tag', $params);
    }


    /**
     * 编辑客户企业标签.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92118
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function markTags(array $params)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/mark_tag', $params);
    }
}
