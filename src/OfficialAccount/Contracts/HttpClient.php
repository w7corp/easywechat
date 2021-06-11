<?php

namespace EasyWeChat\OfficialAccount\Contracts;

use Symfony\Contracts\HttpClient\HttpClientInterface;

interface HttpClient extends HttpClientInterface
{
    public function withAccessToken(AccessToken $accessToken): static;
}
