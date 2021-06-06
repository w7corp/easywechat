<?php

namespace EasyWeChat\OfficialAccount\Contracts;

use Psr\Http\Message\ResponseInterface;

interface Response extends ResponseInterface
{
    public static function success(string $body);
    public static function failed(string $remark, int $status, array $headers);
    public static function replay(array $attributes, Application $application, array $appends = []);
}
