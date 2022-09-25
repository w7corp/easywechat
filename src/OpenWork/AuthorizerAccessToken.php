<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Contracts\AccessToken;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Stringable;

class AuthorizerAccessToken implements AccessToken, Stringable
{
    public function __construct(protected string $corpId, protected string $accessToken)
    {
    }

    public function getCorpId(): string
    {
        return $this->corpId;
    }

    public function getToken(): string
    {
        return $this->accessToken;
    }

    public function __toString()
    {
        return $this->accessToken;
    }

    /**
     * @return array<string, string>
     */
    #[Pure]
    #[ArrayShape(['access_token' => 'string'])]
    public function toQuery(): array
    {
        return ['access_token' => $this->getToken()];
    }
}
