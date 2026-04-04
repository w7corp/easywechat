<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use Closure;
use Overtrue\Socialite\Providers\OpenWeWork;

class OAuthProvider extends OpenWeWork
{
    protected ?Closure $suiteAccessTokenResolver = null;

    public function withSuiteAccessTokenResolver(callable $resolver): static
    {
        $this->suiteAccessTokenResolver = $resolver instanceof Closure ? $resolver : Closure::fromCallable($resolver);

        return $this;
    }

    protected function getSuiteAccessToken(): string
    {
        if ($this->suiteAccessTokenResolver) {
            return ($this->suiteAccessTokenResolver)();
        }

        return parent::getSuiteAccessToken();
    }
}
