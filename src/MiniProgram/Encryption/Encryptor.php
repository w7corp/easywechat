<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Encryptor.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\MiniProgram\Encryption;

use EasyWeChat\Encryption\EncryptionException;
use EasyWeChat\Encryption\Encryptor as BaseEncryptor;
use Exception;

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
     */
    public function decryptData($sessionKey, $iv, $encrypted)
    {
        try {
            $decrypted = openssl_decrypt(
                base64_decode($encrypted, true), 'aes-128-cbc', base64_decode($sessionKey, true),
                OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, base64_decode($iv, true)
            );
        } catch (Exception $e) {
            throw new EncryptionException($e->getMessage(), EncryptionException::ERROR_DECRYPT_AES);
        }

        if (is_null($result = json_decode($this->decode($decrypted), true))) {
            throw new EncryptionException('ILLEGAL_BUFFER', EncryptionException::ILLEGAL_BUFFER);
        }

        return $result;
    }
}
