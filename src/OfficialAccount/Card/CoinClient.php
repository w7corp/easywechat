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

/**
 * Class CoinClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class CoinClient extends BaseClient
{
    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function activate()
    {
        return $this->httpGet('card/pay/activate');
    }

    /**
     * @param string $cardId
     * @param int    $quantity
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function getPrice(string $cardId, int $quantity)
    {
        return $this->httpPostJson('card/pay/getpayprice', [
            'card_id' => $cardId,
            'quantity' => $quantity,
        ]);
    }

    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function summary()
    {
        return $this->httpGet('card/pay/getcoinsinfo');
    }

    /**
     * @param int $count
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function recharge(int $count)
    {
        return $this->httpPostJson('card/pay/recharge', [
            'coin_count' => 100,
        ]);
    }

    /**
     * @param string $orderId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function order(string $orderId)
    {
        return $this->httpPostJson('card/pay/getorder', ['order_id' => $orderId]);
    }

    /**
     * @param array $filters
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function orders(array $filters)
    {
        return $this->httpPostJson('card/pay/getorderlist', $filters);
    }

    /**
     * @param string $cardId
     * @param string $orderId
     * @param int    $quantity
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function confirm(string $cardId, string $orderId, int $quantity)
    {
        return $this->httpPostJson('card/pay/confirm', [
            'card_id' => $cardId,
            'order_id' => $orderId,
            'quantity' => $quantity,
        ]);
    }
}
