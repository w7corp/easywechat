<?php

namespace EasyWeChat\OfficialAccount\Contracts;

interface AccessToken
{
    public function getToken(): string;
}
