<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;

/**
 * @property string $video
 * @property string $title
 * @property string $media_id
 * @property string $description
 * @property string $thumb_media_id
 */
class Video extends BaseMessage
{

    protected $properties = array('title', 'description', 'media_id', 'thumb_media_id');

    /**
     * 设置视频消息
     *
     * @param string $path
     *
     * @return Overtrue\Wechat\Messages\Video
     */
    public function media($path)
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

    /**
     * 生成主动消息数组
     */
    public function toStaff()
    {
        return array(
                'video'   => array(
                              'title'          => $this->title,
                              'media_id'       => $this->media_id,
                              'description'    => $this->description,
                              'thumb_media_id' => $this->thumb_media_id,
                             ),
               );
    }

    /**
     * 生成回复消息数组
     */
    public function toReply()
    {
        $response = array(
                     'Video' => array(
                                 'MediaId'     => $this->media_id,
                                 'Title'       => $this->title,
                                 'Description' => $this->description,
                                ),
                    );

        return $response;
    }

}