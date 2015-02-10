<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;

/**
 * @property string $media_id
 */
class Voice extends BaseMessage
{

    protected $properties = array('media_id');

    /**
     * 设置语音
     *
     * @param string $path
     *
     * @return Overtrue\Wechat\Messages\Voice
     */
    public function media($path)
    {
        $this->attributes['media_id'] = Media::voice($path);

        return $this;
    }

    /**
     * 生成主动消息数组
     */
    public function toStaff()
    {
        return array(
                'voice' => array(
                            'media_id' => $this->media_id,
                           ),
               );
    }

    /**
     * 生成回复消息数组
     */
    public function toReply()
    {
        return array(
                'Voice' => array(
                            'MediaId' => $this->media_id,
                           ),
               );
    }

}