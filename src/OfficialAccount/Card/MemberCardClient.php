<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Card;

class MemberCardClient extends Client
{
    /**
     * 会员卡接口激活.
     *
     * @param array $info
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateUser(array $params = [])
    {
        return $this->httpPostJson('card/membercard/updateuser', $params);
    }

    /**
     * 获取用户提交资料.
     *
     * @param string $activateTicket
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getActivationForm($activateTicket)
    {
        $params = [
            'activate_ticket' => $activateTicket,
        ];

        return $this->httpPostJson('card/membercard/activatetempinfo/get', $params);
    }

    /**
     * 获取开卡组件链接接口.
     *
     * @param array $params 包含会员卡ID和随机字符串
     *
     * @return string 开卡组件链接
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getActivateUrl(array $params = [])
    {
        return $this->httpPostJson('card/membercard/activate/geturl', $params);
    }
}
