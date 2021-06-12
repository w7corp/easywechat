<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Contracts;

interface AccessToken
{
    public function getToken(): string;
}
