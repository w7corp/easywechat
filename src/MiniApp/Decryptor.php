<?php

declare(strict_types=1);

namespace EasyWeChat\MiniApp;

use function base64_decode;
use EasyWeChat\Kernel\Exceptions\DecryptException;
use EasyWeChat\Kernel\Support\AesCbc;
use function is_array;
use function json_decode;
use function sprintf;
use Throwable;

class Decryptor
{
    /**
     * @return array<string, mixed>
     *
     * @throws DecryptException
     */
    public static function decrypt(string $sessionKey, string $iv, string $ciphertext): array
    {
        try {
            $decrypted = AesCbc::decrypt(
                $ciphertext,
                base64_decode($sessionKey, false),
                base64_decode($iv, false)
            );

            $decrypted = json_decode($decrypted, true);

            if (! $decrypted || ! is_array($decrypted)) {
                throw new DecryptException('The given payload is invalid.');
            }
        } catch (Throwable $e) {
            throw new DecryptException(sprintf('The given payload is invalid: %s', $e->getMessage()));
        }

        return $decrypted;
    }
}
