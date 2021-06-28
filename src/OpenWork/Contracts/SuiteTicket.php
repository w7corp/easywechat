<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Contracts;

interface SuiteTicket
{
    public function getTicket(): string;
}
