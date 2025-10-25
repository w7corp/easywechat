<?php

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Encryptor;

class SuiteEncryptor extends Encryptor
{
    #[\JetBrains\PhpStorm\Pure]
    public function __construct(string $suiteId, string $token, string $aesKey)
    {
        parent::__construct($suiteId, $token, $aesKey, null);
    }
}
