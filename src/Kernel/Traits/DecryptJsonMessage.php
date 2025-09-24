<?php

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Message;

trait DecryptJsonMessage
{
    public function decryptJsonMessage(
        Message $message,
        Encryptor $encryptor,
        string $signature,
        int|string $timestamp,
        string $nonce
    ): Message {
        $ciphertext = $message->encrypt;

        $this->validateSignature($encryptor->getToken(), $ciphertext, $signature, $timestamp, $nonce);

        $plain = json_decode(
            $encryptor->decrypt(
                ciphertext: $ciphertext,
                msgSignature: $signature,
                nonce: $nonce,
                timestamp: $timestamp
            ), true);
        is_array($plain) && $message->merge($plain);

        return $message;
    }
}
