<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

use Symfony\Contracts\HttpClient\HttpClientInterface;

interface ChainableHttpClient extends HttpClientInterface
{
    public function getUri(): string;
    public function __get(string | int $name): static;
}
