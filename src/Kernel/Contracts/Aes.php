<?php

namespace EasyWeChat\Kernel\Contracts;

interface Aes
{
    public static function encrypt(string $plaintext, string $key, string $iv = null): string;

    public static function decrypt(string $ciphertext, string $key, string $iv = null): string;
}
