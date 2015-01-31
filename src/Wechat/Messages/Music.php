<?php namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;

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

    public function formatToClient()
    {
        return array(
                "touser"  => $this->to,
                "msgtype" => "music",
                "music"    => array(
                                 "title"          => $this->title,
                                 "description"    => $this->description,
                                 "musicurl"       => $this->url,
                                 "hqmusicurl"     => $this->hq_url,
                                 "thumb_media_id" => $this->thumb_media_id,
                            ),
        );
    }

    public function formatToServer()
    {
        $format = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[music]]></MsgType>
                    <Music>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <MusicUrl><![CDATA[%s]]></MusicUrl>
                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                    </Music>
                    </xml>';

        return sprintf($format, $this->to, $this->from, time(), $this->title,
                $this->description, $this->url, $this->hq_url, $this->thumb_media_id);
    }

}