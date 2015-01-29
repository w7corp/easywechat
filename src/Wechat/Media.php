<?php namespace Overtrue\Wechat;


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
        $url = Client::makeUrl('file.upload');

        $params = array(
            'type' => $type,
        );

        $files = array(
            'media' => $path,
        );

        $contents = Client::post($url, $params, $files);

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
<<<<<<< HEAD
        # TODO: 如果filename存在写入文件，否则直接输出
=======
>>>>>>> 65ffcd8b26a94506f082b727c53c7a92bccd0f81
        $url = Client::makeUrl('file.download');

        $params = array(
            'media_id' => $mediaId,
        );

        $contents = Client::get($url, $params);

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
    static public function __call($method, $args)
    {
        if (in_array(static::$allowTypes, $method)) {

            $args = array($method, array_shift($args));

            return forward_static_call_array(array(__CLASS__, 'upload'), $args);
        }
    }
}