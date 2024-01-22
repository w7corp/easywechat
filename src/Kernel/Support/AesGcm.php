<?php

namespace EasyWeChat\Kernel\Support;

use const OPENSSL_RAW_DATA;

use EasyWeChat\Kernel\Contracts\Aes;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

use function base64_decode;
use function base64_encode;
use function openssl_decrypt;
use function openssl_encrypt;
use function openssl_error_string;

class AesGcm implements Aes
{
    public const BLOCK_SIZE = 16;

    /**
     * @throws InvalidArgumentException
     */
    public static function encrypt(string $plaintext, string $key, ?string $iv = null, string $aad = ''): string
    {
        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            (string) $iv,
            $tag,
            $aad,
            self::BLOCK_SIZE
        );

        if ($ciphertext === false) {
            throw new InvalidArgumentException(openssl_error_string() ?: 'Encrypt failed');
        }

        return base64_encode($ciphertext.$tag);
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function decrypt(string $ciphertext, string $key, ?string $iv = null, string $aad = ''): string
    {
        $ciphertext = base64_decode($ciphertext);

        $tag = substr($ciphertext, -self::BLOCK_SIZE);

        $ciphertext = substr($ciphertext, 0, -self::BLOCK_SIZE);

        $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, (string) $iv, $tag, $aad);

        if ($plaintext === false) {
            throw new InvalidArgumentException(openssl_error_string() ?: 'Decrypt failed');
        }

        return $plaintext;
    }
}
