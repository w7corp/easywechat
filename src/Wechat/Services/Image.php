<?php

namespace Overtrue\Wechat\Services;

use Overtrue\Wechat\Wechat;
use Overtrue\Wechat\Exception;

class Image
{
    const API_UPLOAD = 'https://file.api.weixin.qq.com/cgi-bin/media/uploadimg';


    /**
     * 上传媒体文件
     *
     * @param string $path
     *
     * @return string
     */
    public function upload($path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new Exception("文件不存在或不可读 '$path'");
        }

        $options = array(
                    'files' => array(
                                'media' => $path,
                               ),
                   );

        $contents = Wechat::request('POST', self::API_UPLOAD, array(), $options);

        return $contents['url'];
    }
}