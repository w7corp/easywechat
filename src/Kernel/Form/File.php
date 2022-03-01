<?php

namespace EasyWeChat\Kernel\Form;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use Symfony\Component\Mime\Part\DataPart;

class File extends DataPart
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public static function withContents(string $contents, string $filename, string $contentType = null, string $encoding = null): DataPart
    {
        $path = \tempnam(\sys_get_temp_dir(), 'part_');

        if (!$path) {
            throw new RuntimeException('Unable to create a temporary file.');
        }

        \file_put_contents($path, $contents);

        return self::fromPath($path, $filename, $contentType);
    }
}
