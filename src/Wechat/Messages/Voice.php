<?php Overtrue\Wechat\Messages;


class Voice extends AbstractMessage implements MessageInterface {

    /**
     * 设置语音
     *
     * @param string $path
     *
     * @return Overtrue\Wechat\Messages\Voice
     */
    public function voice($path)
    {
        $this->attributes['media_id'] = Media::voice($path);

        return $this;
    }

    public function formatToClient() {

    }

    public function formatToServer() {

    }

}