<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * File.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Support;

use finfo;

/**
 * Class File.
 */
class File
{
    /**
     * MIME mapping.
     *
     * @var array
     */
    protected static $extensionMap = [
        'audio/wav' => '.wav',
        'audio/x-ms-wma' => '.wma',
        'video/x-ms-wmv' => '.wmv',
        'video/mp4' => '.mp4',
        'audio/mpeg' => '.mp3',
        'audio/amr' => '.amr',
        'application/vnd.rn-realmedia' => '.rm',
        'audio/mid' => '.mid',
        'image/bmp' => '.bmp',
        'image/gif' => '.gif',
        'image/png' => '.png',
        'image/tiff' => '.tiff',
        'image/jpeg' => '.jpg',

                // 列举更多的文件 mime, 企业号是支持的, 公众平台这边之后万一也更新了呢
        'application/msword' => '.doc',

        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => '.dotx',
        'application/vnd.ms-word.document.macroEnabled.12' => '.docm',
        'application/vnd.ms-word.template.macroEnabled.12' => '.dotm',

        'application/vnd.ms-excel' => '.xls',

        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => '.xltx',
        'application/vnd.ms-excel.sheet.macroEnabled.12' => '.xlsm',
        'application/vnd.ms-excel.template.macroEnabled.12' => '.xltm',
        'application/vnd.ms-excel.addin.macroEnabled.12' => '.xlam',
        'application/vnd.ms-excel.sheet.binary.macroEnabled.12' => '.xlsb',

        'application/vnd.ms-powerpoint' => '.ppt',

        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '.pptx',
        'application/vnd.openxmlformats-officedocument.presentationml.template' => '.potx',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => '.ppsx',
        'application/vnd.ms-powerpoint.addin.macroEnabled.12' => '.ppam',
    ];

    /**
     * File header signatures.
     *
     * @var array
     */
    protected static $signatures = [
        'ffd8ff' => '.jpg',
        '424d' => '.bmp',
        '47494638' => '.gif',
        '89504e47' => '.png',
        '494433' => '.mp3',
        'fffb' => '.mp3',
        'fff3' => '.mp3',
        '3026b2758e66cf11' => '.wma',
        '52494646' => '.wav',
        '57415645' => '.wav',
        '41564920' => '.avi',
        '000001ba' => '.mpg',
        '000001b3' => '.mpg',
        '2321414d52' => '.amr',
    ];

    /**
     * Return steam extension.
     *
     * @param string $stream
     *
     * @return string|false
     */
    public static function getStreamExt($stream)
    {
        try {
            if (is_readable($stream)) {
                $stream = file_get_contents($stream);
            }
        } catch (\Exception $e) {
            return false;
        }

        $finfo = new finfo(FILEINFO_MIME);

        $mime = strstr($finfo->buffer($stream), ';', true);

        return isset(self::$extensionMap[$mime]) ? self::$extensionMap[$mime] : self::getExtBySignature($stream);
    }

    /**
     * Get file extension by file header signature.
     *
     * @param string $stream
     *
     * @return string
     */
    public static function getExtBySignature($stream)
    {
        $prefix = strval(bin2hex(mb_strcut($stream, 0, 10)));

        foreach (self::$signatures as $signature => $extension) {
            if (0 === strpos($prefix, strval($signature))) {
                return $extension;
            }
        }

        return '';
    }
}
