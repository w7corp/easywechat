<?php

namespace EasyWeChat\Kernel\Support;

class Str
{
    /**
     * From https://github.com/laravel/framework/blob/9.x/src/Illuminate/Support/Str.php#L632-L644
     * @throws \Exception
     */
    public static function random(int $length = 16): string
    {
        $string = '';

        while (($len = \strlen($string)) < $length) {
            $size = $length - $len;

            /** @phpstan-ignore-next-line  */
            $bytes = \random_bytes($size);

            $string .= \substr(\str_replace(['/', '+', '='], '', \base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
