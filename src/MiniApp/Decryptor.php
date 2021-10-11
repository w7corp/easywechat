<?php

declare(strict_types=1);

namespace EasyWeChat\MiniApp;

use EasyWeChat\Kernel\Exceptions\DecryptException;
use EasyWeChat\Kernel\Support\AesCbc;

class Decryptor
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException|\EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public static function decrypt(string $sessionKey, string $iv, string $ciphertext): array
    {
        $decrypted = AesCbc::decrypt(
            $ciphertext,
            base64_decode($sessionKey, false),
            base64_decode($iv, false)
        );

        $decrypted = json_decode($decrypted, true);

        if (!$decrypted) {
            throw new DecryptException('The given payload is invalid.');
        }

        return $decrypted;
    }
}
