<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Card;

class BoardingPassClient extends Client
{
    /**
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkin(array $params)
    {
        return $this->httpPostJson('card/boardingpass/checkin', $params);
    }
}
