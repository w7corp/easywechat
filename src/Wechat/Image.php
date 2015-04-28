<?php

namespace Overtrue\Wechat;

/**
 * 图片上传服务
 */
class Image
{
    const API_UPLOAD = 'https://file.api.weixin.qq.com/cgi-bin/media/uploadimg';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

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

        $contents = $this->http->post(self::API_UPLOAD, array(), $options);

        return $contents['url'];
    }
}
