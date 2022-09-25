<?php

namespace EasyWeChat\Kernel\Support;

use function base64_decode;
use EasyWeChat\Kernel\Contracts\Aes;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use function openssl_decrypt;
use function openssl_error_string;
use const OPENSSL_RAW_DATA;

class AesEcb implements Aes
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public static function encrypt(string $plaintext, string $key, string $iv = null): string
    {
        $ciphertext = \openssl_encrypt($plaintext, 'aes-256-ecb', $key, OPENSSL_RAW_DATA, (string) $iv);

        if (false === $ciphertext) {
            throw new InvalidArgumentException(openssl_error_string() ?: 'Encrypt AES ECB failed.');
        }

        return \base64_encode($ciphertext);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public static function decrypt(string $ciphertext, string $key, string $iv = null): string
    {
        $plaintext = openssl_decrypt(
            base64_decode($ciphertext, true) ?: '',
            'aes-256-ecb',
            $key,
            OPENSSL_RAW_DATA,
            (string) $iv
        );

        if (false === $plaintext) {
            throw new InvalidArgumentException(openssl_error_string() ?: 'Decrypt AES ECB failed.');
        }

        return $plaintext;
    }
}
