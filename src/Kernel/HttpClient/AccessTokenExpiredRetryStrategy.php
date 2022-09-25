<?php

namespace EasyWeChat\Kernel\HttpClient;

use Closure;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\RefreshableAccessToken as RefreshableAccessTokenInterface;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AccessTokenExpiredRetryStrategy extends GenericRetryStrategy
{
    protected AccessTokenInterface $accessToken;

    protected ?Closure $decider = null;

    public function withAccessToken(AccessTokenInterface $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function decideUsing(Closure $decider): self
    {
        $this->decider = $decider;

        return $this;
    }

    public function shouldRetry(
        AsyncContext $context,
        ?string $responseContent,
        ?TransportExceptionInterface $exception
    ): ?bool {
        if ((bool) $responseContent && $this->decider && ($this->decider)($context, $responseContent, $exception)) {
            if ($this->accessToken instanceof RefreshableAccessTokenInterface) {
                return (bool) $this->accessToken->refresh();
            }

            return false;
        }

        return parent::shouldRetry($context, $responseContent, $exception);
    }
}
