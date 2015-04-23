<?php
namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;

/**
 * 图片消息
 *
 * @property string $media_id
 */
class Image extends BaseMessage
{

    protected $properties = array('media_id');

    /**
     * 媒体
     *
     * @var Overtrue\Wechat\Media
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
     * 设置图片
     *
     * @param string $path
     *
     * @return Image
     */
    public function media($path)
    {
        error_log($this->media->image($path));exit;
        $this->setAttribute('media_id', $this->media->image($path));

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