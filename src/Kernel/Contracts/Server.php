<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

use Psr\Http\Message\ResponseInterface;

/**
 * @method mixed withDefaultSuiteTicketHandler(callable $handler) used in \EasyWeChat\OpenWork\Server
 */
interface Server
{
    public function serve(): ResponseInterface;
}
