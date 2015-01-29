<?php Overtrue\Wechat\Messages;


class Music extends AbstractMessage implements MessageInterface {

    protected $properties = array('url', 'hq_url', 'title', 'description');

    /**
     * 设置音乐消息封面图
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