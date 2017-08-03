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
     * @param string $cardId
     * @param array  $code
     *
     * @return mixed
     */
    public function deposit($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->httpPostJson('card/code/deposit', $params);
    }

    /**
     * 查询导入code数目.
     *
     * @param string $cardId
     *
     * @return mixed
     */
    public function getDepositedCount($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/code/getdepositcount', $params);
    }

    /**
     * 核查code接口.
     *
     * @param string $cardId
     * @param array  $code
     *
     * @return mixed
     */
    public function check($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->httpPostJson('card/code/checkcode', $params);
    }

    /**
     * 查询Code接口.
     *
     * @param string $code
     * @param bool   $checkConsume
     * @param string $cardId
     *
     * @return mixed
     */
    public function get($code, $checkConsume, $cardId)
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
     * @param string $code
     * @param string $newCode
     * @param array  $cardId
     *
     * @return mixed
     */
    public function update($code, $newCode, $cardId = [])
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
     * @param string $code
     * @param string $cardId
     *
     * @return mixed
     */
    public function disable($code, $cardId = '')
    {
        $params = [
            'code' => $code,
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/code/unavailable', $params);
    }

    /**
     * 核销Code接口.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return mixed
     */
    public function consume($code, $cardId = null)
    {
        $params = [
            'code' => $code,
        ];

        if ($cardId) {
            $params['card_id'] = $cardId;
        }

        return $this->httpPostJson('card/code/consume', $params);
    }

    /**
     * Code解码接口.
     *
     * @param string $encryptedCode
     *
     * @return mixed
     */
    public function decrypt($encryptedCode)
    {
        $params = [
            'encrypt_code' => $encryptedCode,
        ];

        return $this->httpPostJson('card/code/decrypt', $params);
    }
}
