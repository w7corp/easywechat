<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

interface MediaInterface extends MessageInterface
{
    public function getMediaId(): string;
}
