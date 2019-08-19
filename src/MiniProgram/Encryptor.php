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
use EasyWeChat\Kernel\Exceptions\DecryptException;
use EasyWeChat\Kernel\Support\AES;
use Safe\Exceptions\JsonException;
use Safe\Exceptions\OpensslException;

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
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \Safe\Exceptions\StringsException
     * @throws \Safe\Exceptions\UrlException
     */
    public function decryptData(string $sessionKey, string $iv, string $encrypted): array
    {
        try {
            $decrypted = AES::decrypt(
                \Safe\base64_decode($encrypted, false), \Safe\base64_decode($sessionKey, false), \Safe\base64_decode($iv, false)
            );

            return \Safe\json_decode($this->pkcs7Unpad($decrypted), true);
        } catch (JsonException | OpensslException $exception) {
            throw new DecryptException('The given payload is invalid.', 0, $exception);
        }
    }
}
