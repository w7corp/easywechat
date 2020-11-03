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
 * Interface MessageInterface.
 *
 * @author overtrue <i@overtrue.me>
 */
interface MessageInterface
{
    public function getType(): string;

    public function transformForJsonRequest(): array;

    public function transformToXml(): string;
}
