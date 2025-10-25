<?php

namespace EasyWeChat\Work;

class Encryptor extends \EasyWeChat\Kernel\Encryptor
{
    #[\JetBrains\PhpStorm\Pure]
    public function __construct(string $corpId, string $token, string $aesKey)
    {
        parent::__construct($corpId, $token, $aesKey, null);
    }
}
