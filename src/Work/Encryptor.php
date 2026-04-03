<?php

namespace EasyWeChat\Work;

use JetBrains\PhpStorm\Pure;

class Encryptor extends \EasyWeChat\Kernel\Encryptor
{
    #[Pure]
    public function __construct(string $corpId, string $token, string $aesKey)
    {
        parent::__construct($corpId, $token, $aesKey, null);
    }
}
