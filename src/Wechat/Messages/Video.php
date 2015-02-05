<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;
use Overtrue\Wechat\Utils\XML;

class Video extends AbstractMessage implements MessageInterface
{

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
        $this->attributes['media_id'] = Media::video($path);

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

    public function formatToClient()
    {
        return array(
                'touser'  => $this->to,
                'msgtype' => 'video',
                'video'   => array(
                              'media_id'       => $this->media_id,
                              'thumb_media_id' => $this->thumb_media_id,
                              'title'          => $this->title,
                              'description'    => $this->description,
                             ),
               );
    }

    public function formatToServer()
    {
        $response = array(
                     'ToUserName'   => $this->to,
                     'FromUserName' => $this->from,
                     'CreateTime'   => time(),
                     'MsgType'      => 'video',
                     'Video'        => array(
                                        'MediaId'     => $this->media_id,
                                        'Title'       => $this->title,
                                        'Description' => $this->description,
                                       ),
                    );

        return XML::build($response);
    }

}