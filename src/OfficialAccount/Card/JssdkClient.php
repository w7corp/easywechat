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

use EasyWeChat\BasicService\Jssdk\Client as Jssdk;
use EasyWeChat\Kernel\Support\Arr;
use function EasyWeChat\Kernel\Support\str_random;

/**
 * Class Jssdk.
 *
 * @author overtrue <i@overtrue.me>
 */
class JssdkClient extends Jssdk
{
    /**
     * @param bool   $refresh
     * @param string $type
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     * @throws \Safe\Exceptions\SplException
     * @throws \Safe\Exceptions\StringsException
     */
    public function getTicket(bool $refresh = false, string $type = 'wx_card'): array
    {
        return parent::getTicket($refresh, $type);
    }

    /**
     * 微信卡券：JSAPI 卡券发放.
     *
     * @param array $cards
     *
     * @return string
     *
     * @throws \Safe\Exceptions\JsonException
     */
    public function assign(array $cards)
    {
        return \Safe\json_encode(array_map(function ($card) {
            return $this->attachExtension($card['card_id'], $card);
        }, $cards));
    }

    /**
     * 生成 js添加到卡包 需要的 card_list 项.
     *
     * @param string $cardId
     * @param array  $extension
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Safe\Exceptions\ArrayException
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     * @throws \Safe\Exceptions\SplException
     * @throws \Safe\Exceptions\StringsException
     */
    public function attachExtension($cardId, array $extension = [])
    {
        $timestamp = time();
        $nonce = str_random(6);
        $ticket = $this->getTicket()['ticket'];

        $ext = array_merge(['timestamp' => $timestamp, 'nonce_str' => $nonce], Arr::only(
            $extension,
            ['code', 'openid', 'outer_id', 'balance', 'fixed_begintimestamp', 'outer_str']
        ));

        $ext['signature'] = $this->dictionaryOrderSignature($ticket, $timestamp, $cardId, $ext['code'] ?? '', $ext['openid'] ?? '', $nonce);

        return [
            'cardId' => $cardId,
            'cardExt' => \Safe\json_encode($ext),
        ];
    }
}
