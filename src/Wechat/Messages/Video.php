<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Wechat;

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
     * @return Video
     */
    public function media($path)
    {
        $this->setAttribute('media_id', Wechat::media()->video($path));

        return $this;
    }

    /**
     * 设置视频封面
     *
     * @param string $path
     *
     * @return Video
     */
    public function thumb($path)
    {
        $this->setAttribute('thumb_media_id', Wechat::media()->thumb($path));

        return $this;
    }

    /**
     * 生成主动消息数组
     *
     * @return array
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
     *
     * @return array
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