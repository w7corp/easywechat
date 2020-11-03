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
 * Class CodeClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class CodeClient extends BaseClient
{
    /**
     * 导入code接口.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deposit(string $cardId, array $codes)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $codes,
        ];

        return $this->httpPostJson('card/code/deposit', $params);
    }

    /**
     * 查询导入code数目.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDepositedCount(string $cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/code/getdepositcount', $params);
    }

    /**
     * 核查code接口.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function check(string $cardId, array $codes)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $codes,
        ];

        return $this->httpPostJson('card/code/checkcode', $params);
    }

    /**
     * 查询 Code 接口.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $code, string $cardId = '', bool $checkConsume = true)
    {
        $params = [
            'code' => $code,
            'check_consume' => $checkConsume,
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/code/get', $params);
    }

    /**
     * 更改Code接口.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $code, string $newCode, string $cardId = '')
    {
        $params = [
            'code' => $code,
            'new_code' => $newCode,
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/code/update', $params);
    }

    /**
     * 设置卡券失效.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function disable(string $code, string $cardId = '')
    {
        $params = [
            'code' => $code,
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/code/unavailable', $params);
    }

    /**
     * 核销 Code 接口.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function consume(string $code, string $cardId = null)
    {
        $params = [
            'code' => $code,
        ];

        if (!is_null($cardId)) {
            $params['card_id'] = $cardId;
        }

        return $this->httpPostJson('card/code/consume', $params);
    }

    /**
     * Code解码接口.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function decrypt(string $encryptedCode)
    {
        $params = [
            'encrypt_code' => $encryptedCode,
        ];

        return $this->httpPostJson('card/code/decrypt', $params);
    }
}
