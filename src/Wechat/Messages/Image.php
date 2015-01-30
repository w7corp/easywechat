<?php namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;

class Image extends AbstractMessage implements MessageInterface {

    protected $properties = array('media_id');

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

    public function formatToClient()
    {
        return json_encode(array(
                "touser"  => $this->to,
                "msgtype" => "image",
                "image"    => array(
                                 "media_id" => $this->media_id
                            ),
        ));
    }

    public function formatToServer()
    {
        $format = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    <Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                    </xml>';

        return sprintf($format, $this->to, $this->from, time(), $this->media_id);
    }

}