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

/**
 * Class GeneralCardClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class GeneralCardClient extends Client
{
    /**
     * 通用卡接口激活.
     *
     * @param array $info
     *
     * @return mixed
     */
    public function activate(array $info = [])
    {
        return $this->httpPostJson('card/generalcard/activate', $info);
    }

    /**
     * 通用卡撤销激活.
     *
     * @param string $cardId
     * @param string $code
     *
     * @return mixed
     */
    public function deactivate(string $cardId, string $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->httpPostJson('card/generalcard/unactivate', $params);
    }

    /**
     * 更新会员信息.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function updateUser(array $params = [])
    {
        return $this->httpPostJson('card/generalcard/updateuser', $params);
    }
}
