<?php

namespace EasyWeChat\Kernel\Support;

use EasyWeChat\Kernel\Contracts\Aes;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

class AesCbc implements Aes
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public static function encrypt(string $plaintext, string $key, string $iv = null): string
    {
        $ciphertext = \openssl_encrypt($plaintext, "aes-128-cbc", $key, \OPENSSL_RAW_DATA, $iv);

        if (false === $ciphertext) {
            throw new InvalidArgumentException(\openssl_error_string());
        }

        return base64_encode($ciphertext);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public static function decrypt(string $ciphertext, string $key, string $iv = null): string
    {
        $plaintext = \openssl_decrypt(
            \base64_decode($ciphertext),
            "aes-128-cbc",
            $key,
            \OPENSSL_RAW_DATA,
            $iv
        );

        if (false === $ciphertext) {
            throw new InvalidArgumentException(\openssl_error_string());
        }

        return $plaintext;
    }
}
