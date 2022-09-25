<?php

namespace EasyWeChat\Kernel\Support;

use function base64_encode;
use Exception;
use function preg_replace;
use function random_bytes;
use function str_replace;
use function strlen;
use function strtolower;
use function substr;
use function trim;

class Str
{
    /**
     * From https://github.com/laravel/framework/blob/9.x/src/Illuminate/Support/Str.php#L632-L644
     *
     * @throws Exception
     */
    public static function random(int $length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            /** @phpstan-ignore-next-line */
            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    public static function snakeCase(string $string): string
    {
        return trim(strtolower((string) preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $string)), '_');
    }
}
