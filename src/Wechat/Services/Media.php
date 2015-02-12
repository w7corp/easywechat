<?php

namespace Overtrue\Wechat\Services;

use Exception;
use Overtrue\Wechat\Wechat;

class Media
{

    const API_UPLOAD = 'http://file.api.weixin.qq.com/cgi-bin/media/upload';
    const API_GET    = 'http://file.api.weixin.qq.com/cgi-bin/media/upload';

    /**
     * 允许上传的类型
     *
     * @var array
     */
    protected $allowTypes = array('image', 'voice', 'video', 'thumb');

    /**
     * 上传媒体文件
     *
     * @param string $path
     * @param string $type
     *
     * @return string
     */
    public function upload($type, $path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new Exception("文件不存在或不可读 '$path'");
        }

        if (!in_array($type, $this->allowTypes)) {
            throw new Exception("错误的媒体类型 '{$type}'");
        }

        $queries = array(
                    'type' => $type,
                   );

        $options = array(
                    'files' => array(
                                'media' => $path,
                               ),
                   );

        $url = self::API_UPLOAD . '?' . http_build_query($queries);

        $contents = Wechat::request('POST', $url, array(), $options);

        return $contents['media_id'];
    }

    /**
     * 下载媒体文件
     *
     * @param string $mediaId
     * @param string $filename
     *
     * @return mixed
     */
    public function download($mediaId, $filename = '')
    {
        $params = array(
            'media_id' => $mediaId,
        );

        $contents = Wechat::request('GET', self::API_GET, $params);

        return $filename ? $contents : file_put_contents($filename, $contents);
    }

    /**
     * 魔术调用
     *
     * <pre>
     * $media->image($path); // $media->upload('image', $path);
     * </pre>
     *
     * @param string $method
     * @param array  $args
     *
     * @return string
     */
    public function __call($method, $args)
    {
        $args = array($method, array_shift($args));

        return call_user_func_array(array(__CLASS__, 'upload'), $args);
    }
}