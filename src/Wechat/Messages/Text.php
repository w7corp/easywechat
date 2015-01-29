<?php Overtrue\Wechat\Messages;


class Text extends AbstractMessage implements MessageInterface {

    /**
     * 设置内容
     *
     * @param string $content
     *
     * @return Overtrue\Wechat\Messages\Text
     */
    public function content($content)
    {
        $this->attributes['content'] = $content;

        return $this;
    }

    public function formatToClient() {

    }

    public function formatToServer() {

    }

}