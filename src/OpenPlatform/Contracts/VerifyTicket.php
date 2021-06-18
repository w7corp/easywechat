<?php

namespace EasyWeChat\OpenPlatform\Contracts;

interface VerifyTicket
{
    public function getTicket(): string;
}
