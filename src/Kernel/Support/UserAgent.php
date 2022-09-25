<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Support;

use function array_map;
use function array_unshift;
use function class_exists;
use Composer\InstalledVersions;
use function curl_version;
use function defined;
use function explode;
use function extension_loaded;
use function function_exists;
use function ini_get;

class UserAgent
{
    /**
     * @param  array<string>  $appends
     * @return string
     */
    public static function create(array $appends = []): string
    {
        $value = array_map('strval', $appends);

        if (defined('HHVM_VERSION')) {
            array_unshift($value, 'HHVM/'.HHVM_VERSION);
        }

        $disabledFunctions = explode(',', ini_get('disable_functions') ?: '');

        if (extension_loaded('curl') && function_exists('curl_version')) {
            array_unshift($value, 'curl/'.(curl_version() ?: ['version' => 'unknown'])['version']);
        }

        if (! ini_get('safe_mode')
            && function_exists('php_uname')
            && ! in_array('php_uname', $disabledFunctions, true)
        ) {
            $osName = 'OS/'.php_uname('s').'/'.php_uname('r');
            array_unshift($value, $osName);
        }

        if (class_exists(InstalledVersions::class)) {
            array_unshift($value, 'easywechat-sdk/'.((string) InstalledVersions::getVersion('w7corp/easywechat')));
        }

        return trim(implode(' ', $value));
    }
}
