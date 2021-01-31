<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\Broadcast;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */
class Client extends BaseClient
{
    /**
     * Add broadcast goods.
     *
     * @param array $goodsInfo
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $goodsInfo)
    {
        $params = [
            'goodsInfo' => $goodsInfo,
        ];

        return $this->httpPostJson('wxaapi/broadcast/goods/add', $params);
    }

    /**
     * Reset audit.
     *
     * @param int $auditId
     * @param int $goodsId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function resetAudit(int $auditId, int $goodsId)
    {
        $params = [
            'auditId' => $auditId,
            'goodsId' => $goodsId,
        ];

        return $this->httpPostJson('wxaapi/broadcast/goods/resetaudit', $params);
    }

    /**
     * Resubmit audit goods.
     *
     * @param int $goodsId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function resubmitAudit(int $goodsId)
    {
        $params = [
            'goodsId' => $goodsId,
        ];

        return $this->httpPostJson('wxaapi/broadcast/goods/audit', $params);
    }

    /**
     * Delete broadcast goods.
     *
     * @param int $goodsId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(int $goodsId)
    {
        $params = [
            'goodsId' => $goodsId,
        ];

        return $this->httpPostJson('wxaapi/broadcast/goods/delete', $params);
    }

    /**
     * Update goods info.
     *
     * @param array $goodsInfo
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(array $goodsInfo)
    {
        $params = [
            'goodsInfo' => $goodsInfo,
        ];

        return $this->httpPostJson('wxaapi/broadcast/goods/update', $params);
    }

    /**
     * Get goods information and review status.
     *
     * @param array $goodsIdArray
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGoodsWarehouse(array $goodsIdArray)
    {
        $params = [
            'goods_ids' => $goodsIdArray,
        ];

        return $this->httpPostJson('wxa/business/getgoodswarehouse', $params);
    }

    /**
     * Get goods list based on status
     *
     * @param  array  $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getApproved(array $params)
    {
        return $this->httpGet('wxaapi/broadcast/goods/getapproved', $params);
    }

    /**
     * Add goods to the designated live room.
     *
     * @param  array  $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addGoods(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/addgoods', $params);
    }

    /**
     * Get Room List.
     *
     * @param  int  $start
     * @param  int  $limit
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author onekb <1@1kb.ren>
     */
    public function getRooms(int $start = 0, int $limit = 10)
    {
        $params = [
            'start' => $start,
            'limit' => $limit,
        ];

        return $this->httpPostJson('wxa/business/getliveinfo', $params);
    }

    /**
     * Get Playback List.
     *
     * @param  int  $roomId
     * @param  int  $start
     * @param  int  $limit
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author onekb <1@1kb.ren>
     */
    public function getPlaybacks(int $roomId, int $start = 0, int $limit = 10)
    {
        $params = [
            'action' => 'get_replay',
            'room_id' => $roomId,
            'start' => $start,
            'limit' => $limit,
        ];

        return $this->httpPostJson('wxa/business/getliveinfo', $params);
    }

    /**
     * Create a live room.
     *
     * @param  array  $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createLiveRoom(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/create', $params);
    }

    /**
     * Delete a live room.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function deleteLiveRoom(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/deleteroom', $params);
    }

    /**
     * Update a live room.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateLiveRoom(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/editroom', $params);
    }

    /**
     * Gets the live room push stream url.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getPushUrl(array $params)
    {
        return $this->httpGet('wxaapi/broadcast/room/getpushurl', $params);
    }

    /**
     * Gets the live room share qrcode.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getShareQrcode(array $params)
    {
        return $this->httpGet('wxaapi/broadcast/room/getsharedcode', $params);
    }

    /**
     * Add a live room assistant.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function addAssistant(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/addassistant', $params);
    }

    /**
     * Update a live room assistant.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateAssistant(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/modifyassistant', $params);
    }

    /**
     * Delete a live room assistant.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function deleteAssistant(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/removeassistant', $params);
    }

    /**
     * Gets the assistant list.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getAssistantList(array $params)
    {
        return $this->httpGet('wxaapi/broadcast/room/getassistantlist', $params);
    }

    /**
     * Add the sub anchor.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function addSubAnchor(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/addsubanchor', $params);
    }

    /**
     * Update the sub anchor.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateSubAnchor(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/modifysubanchor', $params);
    }

    /**
     * Delete the sub anchor.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function deleteSubAnchor(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/deletesubanchor', $params);
    }

    /**
     * Gets the sub anchor info.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getSubAnchor(array $params)
    {
        return $this->httpGet('wxaapi/broadcast/room/getsubanchor', $params);
    }

    /**
     * Turn official index on/off.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateFeedPublic(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/updatefeedpublic', $params);
    }

    /**
     * Turn playback status on/off.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateReplay(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/updatereplay', $params);
    }

    /**
     * Turn customer service status on/off.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateKf(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/updatekf', $params);
    }

    /**
     * Turn global comments status on/off.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateComment(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/room/updatecomment', $params);
    }

    /**
     * Add member role.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function addRole(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/role/addrole', $params);
    }

    /**
     * Delete member role.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function deleteRole(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/role/deleterole', $params);
    }

    /**
     * Gets the role list.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getRoleList(array $params)
    {
        return $this->httpGet('wxaapi/broadcast/role/getrolelist', $params);
    }

    /**
     * Gets long-term subscribers.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFollowers(array $params)
    {
        return $this->httpPost('wxa/business/get_wxa_followers', $params);
    }

    /**
     * Sending live broadcast start event to long-term subscribers.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pushMessage(array $params)
    {
        return $this->httpPost('wxa/business/push_message', $params);
    }

    /**
     * Change the status of goods on/off shelves in room.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateGoodsInRoom(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/goods/onsale', $params);
    }

    /**
     * Delete goods in room.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteGoodsInRoom(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/goods/deleteInRoom', $params);
    }

    /**
     * Push goods in room.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pushGoods(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/goods/push', $params);
    }

    /**
     * Change goods sort in room.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sortGoods(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/goods/sort', $params);
    }

    /**
     * Download goods explanation video.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function downloadGoodsExplanationVideo(array $params)
    {
        return $this->httpPost('wxaapi/broadcast/goods/getVideo', $params);
    }
}
