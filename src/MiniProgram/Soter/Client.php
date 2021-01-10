<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Soter;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param string $openid
     * @param string $json
     * @param string $signature
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verifySignature(string $openid, string $json, string $signature)
    {
        return $this->httpPostJson('cgi-bin/soter/verify_signature', [
            'openid' => $openid,
            'json_string' => $json,
            'json_signature' => $signature,
        ]);
    }
}
