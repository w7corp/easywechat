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
 * Class MemberCardClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class MemberCardClient extends Client
{
    /**
     * 会员卡接口激活.
     *
     * @param array $info
     *
     * @return mixed
     */
    public function activate(array $info = [])
    {
        return $this->httpPostJson('card/membercard/activate', $info);
    }

    /**
     * 设置开卡字段接口.
     *
     * @param string $cardId
     * @param array  $settings
     *
     * @return mixed
     */
    public function setActivationForm(string $cardId, array $settings)
    {
        $params = array_merge(['card_id' => $cardId], $settings);

        return $this->httpPostJson('card/membercard/activateuserform/set', $params);
    }

    /**
     * 拉取会员信息接口.
     *
     * @param string $cardId
     * @param string $code
     *
     * @return mixed
     */
    public function getUser(string $cardId, string $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->httpPostJson('card/membercard/userinfo/get', $params);
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
        return $this->httpPostJson('card/membercard/updateuser', $params);
    }
}
