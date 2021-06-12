<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Contracts;

interface Server
{
    public function process(): Response;

    public function buildResponse($response): array;
}
