<?php

namespace Overtrue\Wechat\Services;

use Exception;

class Media extends Service
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

        if (in_array($type, $this->allowTypes)) {
            throw new Exception("错误的媒体类型 '{$type}'");
        }

        $queries = array(
                    'type' => $type,
                   );
        $files = array(
                  'media' => $path,
                 );

        $contents = $this->post(self::API_UPLOAD, array(), $queries, $files);

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

        $contents = $this->get(self::API_GET, $params);

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
    static public function __call($method, $args)
    {
        $args = array($method, array_shift($args));

        return call_user_func_array(array(__CLASS__, 'upload'), $args);
    }
}