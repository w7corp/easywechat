<?php

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Encryptor;
use JetBrains\PhpStorm\Pure;

class SuiteEncryptor extends Encryptor
{
    #[Pure]
    public function __construct(string $suiteId, string $token, string $aesKey)
    {
        parent::__construct($suiteId, $token, $aesKey, null);
    }
}
