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
}
