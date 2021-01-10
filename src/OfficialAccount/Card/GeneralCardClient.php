<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Card;

class GeneralCardClient extends Client
{
    /**
     * 通用卡接口激活.
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
        return $this->httpPostJson('card/generalcard/activate', $info);
    }

    /**
     * 通用卡撤销激活.
     *
     * @param string $cardId
     * @param string $code
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateUser(array $params = [])
    {
        return $this->httpPostJson('card/generalcard/updateuser', $params);
    }
}
