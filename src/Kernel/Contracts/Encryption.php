<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Contracts;

/**
 * Interface Encryption.
 *
 * @author overtrue <i@overtrue.me>
 */
interface Encryption
{
    /**
     * Encrypt $data.
     *
     * @param string $text
     *
     * @return string
     */
    public function encrypt(string $text): string;

    /**
     * Decrypt $cipherText.
     *
     * @param string $cipherText
     *
     * @return string
     */
    public function decrypt(string $cipherText): string;
}
