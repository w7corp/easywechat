<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Security;

use EasyWeChat\Payment\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPublicKey()
    {
        $params = [
            'sign_type' => 'MD5',
        ];

        return $this->safeRequest('https://fraud.mch.weixin.qq.com/risk/getpublickey', $params);
    }
}
