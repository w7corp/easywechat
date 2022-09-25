<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Support;

use TheNorthMemory\Xml\Transformer;

class Xml
{
    public static function parse(string $xml): array|null
    {
        return Transformer::toArray($xml);
    }

    public static function build(array $data): string
    {
        return Transformer::toXml($data);
    }
}
