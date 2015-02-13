<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Wechat;

/**
 * @property string $media_id
 */
class Image extends BaseMessage
{

    protected $properties = array('media_id');

    /**
     * 设置图片
     *
     * @param string $path
     *
     * @return Image
     */
    public function media($path)
    {
        $this->setAttribute('media_id', Wechat::media()->image($path));

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
                'image' => array(
                            'media_id' => $this->media_id,
                           ),
              );
    }

    /**
     * 生成回复消息数组
     * @return array
     */
    public function toReply()
    {
        return array(
                'Image' => array(
                            'MediaId' => $this->media_id,
                           ),
               );
    }

}