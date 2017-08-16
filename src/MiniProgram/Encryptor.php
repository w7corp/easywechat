<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram;

use EasyWeChat\Kernel\Encryptor as BaseEncryptor;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Support\AES;

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
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function decryptData(string $sessionKey, string $iv, string $encrypted): array
    {
        $decrypted = AES::decrypt(
            base64_decode($encrypted, true),
            base64_decode($sessionKey, true),
            base64_decode($iv, true),
            OPENSSL_NO_PADDING
        );

        $result = $this->pkcs7Unpad($decrypted);
        $content = json_decode($result, true);

        if ($content['watermark']['appid'] !== $this->appId) {
            throw new RuntimeException('Invalid appId.', static::ERROR_INVALID_APP_ID);
        }

        return $content;
    }
}
