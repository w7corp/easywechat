<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;

/**
 * 声音消息
 *
 * @property string $media_id
 */
class Voice extends BaseMessage
{
    protected $properties = array('media_id');

    /**
     * 媒体
     *
     * @var \Overtrue\Wechat\Media
     */
    protected $media;

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->media = new Media($appId, $appSecret);
    }

    /**
     * 设置语音
     *
     * @param string $path
     *
     * @return Voice
     */
    public function media($path)
    {
        $this->setAttribute('media_id', $this->media->voice($path));

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
                'voice' => array(
                            'media_id' => $this->media_id,
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
        return array(
                'Voice' => array(
                            'MediaId' => $this->media_id,
                           ),
               );
    }
}
