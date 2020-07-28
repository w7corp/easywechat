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
 * Interface MediaInterface.
 *
 * @author overtrue <i@overtrue.me>
 */
interface MediaInterface extends MessageInterface
{
    public function getMediaId(): string;
}
