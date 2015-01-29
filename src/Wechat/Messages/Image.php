<?php Overtrue\Wechat\Messages;


class Image extends AbstractMessage implements MessageInterface {

    /**
     * 设置图片
     *
     * @param string $path
     *
     * @return Overtrue\Wechat\Messages\Text
     */
    public function image($path)
    {
        //TODO::上传图片
        $this->attributes['image'] = Media::upload($path);
    }

    public function formatToClient() {

    }

    public function formatToServer() {

    }

}