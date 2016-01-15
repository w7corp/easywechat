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
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
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
    protected static $extensionMap = array(
        'application/msword' => '.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
        'application/rtf' => '.rtf',
        'application/vnd.ms-excel' => '.xls',
        'application/x-excel' => '.xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
        'application/vnd.ms-powerpoint' => '.ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '.pptx',
        'application/vnd.ms-powerpoint' => '.pps',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => '.ppsx',
        'application/pdf' => '.pdf',
        'application/x-shockwave-flash' => '.swf',
        'application/x-msdownload' => '.dll',
        'application/octet-stream' => '.exe',
        'application/octet-stream' => '.msi',
        'application/octet-stream' => '.chm',
        'application/octet-stream' => '.cab',
        'application/octet-stream' => '.ocx',
        'application/octet-stream' => '.rar',
        'application/x-tar' => '.tar',
        'application/x-compressed' => '.tgz',
        'application/x-zip-compressed' => '.zip',
        'application/x-compress' => '.z',
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
        'text/plain' => '.txt',
        'text/xml' => '.xml',
        'text/html' => '.html',
        'text/css' => '.css',
        'text/javascript' => '.js',
        'message/rfc822' => '.mhtml',
    );

    /**
     * Return steam extension.
     *
     * @param string $stream
     *
     * @return string
     */
    public static function getStreamExt($stream)
    {
        $finfo = new finfo(FILEINFO_MIME);

        $mime = $finfo->buffer($stream);

        return isset(self::$extensionMap[$mime]) ? self::$extensionMap[$mime] : '';
    }
}
