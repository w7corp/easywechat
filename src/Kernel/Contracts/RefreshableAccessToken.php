<?php

namespace EasyWeChat\Kernel\Contracts;

interface RefreshableAccessToken extends AccessToken
{
    public function refresh(): string;
}
