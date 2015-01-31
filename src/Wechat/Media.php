<?php namespace Overtrue\Wechat;

use Exception;

class Media {

    /**
     * 允许上传的类型
     *
     * @var array
     */
    static protected $allowTypes = array('image', 'voice', 'video', 'thumb');

    /**
     * 上传媒体文件
     *
     * @param string $path
     * @param string $type
     *
     * @return string
     */
    static public function upload($type, $path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new Exception("File not found '$path'");
        }

        $url = Wechat::makeUrl('file.upload', array(
                                                'type' => $type,
                                              ));
        $files = array(
            'media' => $path,
        );

        $contents = Wechat::post($url, [], $files);

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
    static public function download($mediaId, $filename = '')
    {
        $url = Wechat::makeUrl('file.download');

        $params = array(
            'media_id' => $mediaId,
        );

        $contents = Wechat::get($url, $params);

        return $filename ? $contents : file_put_contents($filename, $contents);
    }

    /**
     * 魔术调用
     *
     * <pre>
     * Media::image($path); // == Media::upload('image', $path);
     * </pre>
     *
     * @param string $method
     * @param array  $args
     *
     * @return string
     */
    static public function __callStatic($method, $args)
    {
        if (in_array($method, static::$allowTypes)) {

            $args = array($method, array_shift($args));

            return forward_static_call_array(array(__CLASS__, 'upload'), $args);
        }
    }
}