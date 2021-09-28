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
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
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
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function list(string $userId)
    {
        return $this->httpGet('cgi-bin/externalcontact/list', [
            'userid' => $userId,
        ]);
    }

    /**
     * 获取外部联系人详情.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91556
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
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
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92994
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function batchGetUsers(array $data)
    {
        return $this->httpPostJson(
            'cgi-bin/externalcontact/batch/get_by_user',
            $data
        );
    }

    /**
     * 修改客户备注信息.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92115
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
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
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91563
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUnassigned(int $pageId = 0, int $pageSize = 1000)
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
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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

    /**
     * 获取客户群列表.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92120
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
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCorpTags(array $tagIds = [])
    {
        $params = [
            'tag_id' => $tagIds,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/get_corp_tag_list', $params);
    }

    /**
     * 添加企业客户标签.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92117#添加企业客户标签
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
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateCorpTag(string $id, string $name, int $order = 1)
    {
        $params = [
            'id' => $id,
            'name' => $name,
            'order' => $order,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/edit_corp_tag', $params);
    }

    /**
     * 删除企业客户标签.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92117#删除企业客户标签
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteCorpTag(array $tagId, array $groupId)
    {
        $params = [
            'tag_id' => $tagId,
            'group_id' => $groupId,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/del_corp_tag', $params);
    }

    /**
     * 编辑客户企业标签.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92118
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
