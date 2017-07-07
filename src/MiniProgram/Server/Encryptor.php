<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\Encryption;

use EasyWeChat\Kernel\Encryptor as BaseEncryptor;
use Exception;

/**
 * Class Encryptor.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Encryptor extends BaseEncryptor
{
    /**
     * Decrypt data.
     *
     * @param string $sessionKey
     * @param string $iv
     * @param string $encrypted
     *
     * @return array
     *
     * @throws EncryptionException
     */
    public function decryptData($sessionKey, $iv, $encrypted)
    {
        try {
            $decrypted = openssl_decrypt(
                base64_decode($encrypted, true), 'aes-128-cbc', base64_decode($sessionKey, true),
                OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, base64_decode($iv, true)
            );
        } catch (Exception $e) {
            throw new EncryptionException($e->getMessage(), BaseEncryptor::ERROR_DECRYPT_AES);
        }

        if (is_null($result = json_decode($this->decode($decrypted), true))) {
            throw new EncryptionException('ILLEGAL_BUFFER', BaseEncryptor::ILLEGAL_BUFFER);
        }

        return $result;
    }
}
