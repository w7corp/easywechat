<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Support;

class AES
{
    public static function encrypt(string $text, string $key, string $iv, int $option = OPENSSL_RAW_DATA): string
    {
        self::assertKeyIsValid($key);
        self::assertIvIsValid($iv);

        return openssl_encrypt($text, self::getMode($key), $key, $option, $iv);
    }

    public static function decrypt(string $cipherText, string $key, string $iv, int $option = OPENSSL_RAW_DATA, $method = null): string
    {
        self::assertKeyIsValid($key);
        self::assertIvIsValid($iv);

        return openssl_decrypt($cipherText, $method ?: self::getMode($key), $key, $option, $iv);
    }

    public static function getMode($key): string
    {
        return 'aes-'.(8 * strlen($key)).'-cbc';
    }

    public static function assertKeyIsValid(string $key)
    {
        if (!in_array(strlen($key), [16, 24, 32], true)) {
            throw new \InvalidArgumentException(sprintf('Key length must be 16, 24, or 32 bytes; got key len (%s).', strlen($key)));
        }
    }

    public static function assertIvIsValid(string $iv)
    {
        if (!empty($iv) && 16 !== strlen($iv)) {
            throw new \InvalidArgumentException('IV length must be 16 bytes.');
        }
    }
}
