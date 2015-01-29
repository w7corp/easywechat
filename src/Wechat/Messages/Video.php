<?php Overtrue\Wechat\Messages;


class Video extends AbstractMessage implements MessageInterface {

    protected $properties = array('video', 'title', 'description');

    /**
     * 设置视频消息
     *
     * @param string $path
     *
     * @return Overtrue\Wechat\Messages\Video
     */
    public function video($path)
    {
        $this->attributes['media_id'] = Media::voice($path);

        return $this;
    }

    /**
     * 设置视频封面
     *
     * @param string $path
     *
     * @return Overtrue\Wechat\Messages\Music
     */
    public function thumb($path)
    {
        $this->attributes['thumb_media_id'] = Media::thumb($path);

        return $this;
    }

    public function formatToClient() {

    }

    public function formatToServer() {

    }

}