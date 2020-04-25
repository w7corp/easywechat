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
     * Add broadcast goods
     *
     * @param string $coverImgUrl
     * @param string $name
     * @param int $priceType
     * @param float $price
     * @param float $price2
     * @param string $url
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(string $coverImgUrl, string $name, int $priceType, float $price, float $price2, string $url)
    {
        $params = $this->getGoodsInfoArray($coverImgUrl, $name, $priceType, $price, $price2, $url);
        
        return $this->httpPostJson('wxaapi/broadcast/goods/add', $params);
    }
    
    /**
     * Reset audit
     *
     * @param int $auditId
     * @param int $goodsId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function resetAudit(int $auditId, int $goodsId)
    {
        $params = [
            'auditId' => $auditId,
            'goodsId' => $goodsId
        ];
        
        return $this->httpPostJson('wxaapi/broadcast/goods/resetaudit', $params);
    }
    
    /**
     * Resubmit audit goods
     *
     * @param int $goodsId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reSubmitAudit(int $goodsId)
    {
        $params = [
            'goodsId' => $goodsId
        ];
        
        return $this->httpPostJson('wxaapi/broadcast/goods/audit', $params);
    }
    
    /**
     * Delete broadcast goods
     *
     * @param int $goodsId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(int $goodsId)
    {
        $params = [
            'goodsId' => $goodsId
        ];
        
        return $this->httpPostJson('wxaapi/broadcast/goods/delete', $params);
    }
    
    /**
     * Update goods info
     *
     * @param string $coverImgUrl
     * @param string $name
     * @param int $priceType
     * @param float $price
     * @param float $price2
     * @param string $url
     * @param int $goodsId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $coverImgUrl, string $name, int $priceType, float $price, float $price2, string $url, int $goodsId)
    {
        $params = $this->getGoodsInfoArray($coverImgUrl, $name, $priceType, $price, $price2, $url, $goodsId);
        
        return $this->httpPostJson('wxaapi/broadcast/goods/update', $params);
    }
    
    /**
     * Get goods information and review status
     *
     * @param array $goodsIdArray
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGoodsWareHouse(array $goodsIdArray)
    {
        $params = [
            'goods_ids' => $goodsIdArray
        ];
        
        return $this->httpGet('wxa/business/getgoodswarehouse', $params);
    }
    
    /**
     * Get goods info array
     *
     * @param string $coverImgUrl
     * @param string $name
     * @param int $priceType
     * @param float $price
     * @param float $price2
     * @param string $url
     * @param int $goodsId
     * @return array
     */
    private function getGoodsInfoArray(string $coverImgUrl, string $name, int $priceType, float $price, float $price2, string $url, $goodsId = 0)
    {
        $goodsInfoArray = [
            'coverImgUrl' => $coverImgUrl,
            'name' => $name,
            'priceType' => $priceType,
            'price' => $price,
            'url' => $url,
        ];
    
        if ($priceType == 1) {
            $goodsInfoArray['price2'] = $price2;
        }
    
        if ($goodsId) {
            $goodsInfoArray['goodsId'] = $goodsId;
        }
    
        return ['goodsInfo' => $goodsInfoArray];
    }
}
