<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use Closure;

trait ResetsResolvedDependencies
{
    /**
     * @param  iterable<array{0: bool, 1: Closure(): mixed}>  $dependencies
     */
    protected function resetResolvedDependencies(iterable $dependencies): void
    {
        foreach ($dependencies as [$usesCustomDependency, $resetter]) {
            if (! $usesCustomDependency) {
                $resetter();
            }
        }
    }
}
