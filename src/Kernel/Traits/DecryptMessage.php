<?php

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\Support\MessageParser;

trait DecryptMessage
{
    /**
     * Decrypt message (automatically detects XML or JSON format).
     *
     * @throws RuntimeException
     * @throws BadRequestException
     */
    public function decryptMessage(
        Message $message,
        Encryptor $encryptor,
        string $signature,
        int|string $timestamp,
        string $nonce
    ): Message {
        $ciphertext = $message->Encrypt ?? $message->encrypt ?? null;

        if (! is_string($ciphertext) || $ciphertext === '') {
            throw new BadRequestException('Request ciphertext must not be empty.');
        }

        $this->validateSignature($encryptor->getToken(), $ciphertext, $signature, $timestamp, $nonce);

        $plaintext = $encryptor->decrypt(
            ciphertext: $ciphertext,
            msgSignature: $signature,
            nonce: $nonce,
            timestamp: $timestamp
        );

        $attributes = MessageParser::parse($plaintext);

        $message->merge($attributes);

        return $message;
    }

    /**
     * Validate the request signature.
     *
     * @throws BadRequestException
     */
    protected function validateSignature(
        string $token,
        string $ciphertext,
        string $signature,
        int|string $timestamp,
        string $nonce
    ): void {
        if (empty($signature)) {
            throw new BadRequestException('Request signature must not be empty.');
        }

        $params = [$token, $timestamp, $nonce, $ciphertext];

        sort($params, SORT_STRING);

        if ($signature !== sha1(implode($params))) {
            throw new BadRequestException('Invalid request signature.');
        }
    }
}
