<?php namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;

class Voice extends AbstractMessage implements MessageInterface {

    protected $properties = array('media_id');

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

    public function formatToClient()
    {
        return array(
                "touser"  => $this->to,
                "msgtype" => "voice",
                "voice"    => array(
                                 "media_id" => $this->media_id
                            ),
        );
    }

    public function formatToServer()
    {
        $format = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    <Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                    </xml>';

        return sprintf($format, $this->to, $this->from, time(), $this->media_id);
    }

}