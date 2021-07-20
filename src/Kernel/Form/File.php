<?php

namespace EasyWeChat\Kernel\Form;

use Symfony\Component\Mime\Part\DataPart;

class File extends DataPart
{
    public static function withContents(string $contents, string $filename, string $contentType = null, string $encoding = null): DataPart
    {
        $path = \tempnam(\sys_get_temp_dir(), 'part_');

        \file_put_contents($path, $contents);

        return self::fromPath($path, $filename, $contentType);
    }
}
