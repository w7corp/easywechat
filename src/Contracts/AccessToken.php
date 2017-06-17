<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Contracts;

/**
 * Interface AccessToken.
 *
 * @author overtrue <i@overtrue.me>
 */
interface AccessToken
{
    /**
     * @param bool $force
     *
     * @return bool
     */
    public function refresh(bool $force = false): bool;

    /**
     * @return array
     */
    public function getQuery(): array;
}
