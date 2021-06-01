<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Exceptions\RuntimeException;

function dd()
{
    \dump(...\func_get_args());
    exit;
}

function throw_if($condition, $exception = 'RuntimeException', ...$parameters): mixed
{
    if ($condition) {
        if (is_string($exception) && class_exists($exception)) {
            $exception = new $exception(...$parameters);
        }

        throw is_string($exception) ? new RuntimeException($exception) : $exception;
    }

    return $condition;
}
