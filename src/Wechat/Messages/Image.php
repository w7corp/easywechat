<?php Overtrue\Wechat\Messages;


class Image extends AbstractMessage implements MessageInterface {

    /**
     * 设置图片
     *
     * @param string $path
     *
     * @return Overtrue\Wechat\Messages\Image
     */
    public function image($path)
    {
        $this->attributes['media_id'] = Media::image($path);

        return $this;
    }

    public function formatToClient() {

    }

    public function formatToServer() {

    }

}