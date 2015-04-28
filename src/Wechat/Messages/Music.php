<?php

namespace Overtrue\Wechat\Messages;

/**
 * 音乐消息
 *
 * @property string $url
 * @property string $hq_url
 * @property string $title
 * @property string $description
 * @property string $thumb_media_id
 */
class Music extends BaseMessage
{
    protected $properties = array(
                             'url',
                             'hq_url',
                             'title',
                             'description',
                             'thumb_media_id',
                            );

    /**
     * 设置音乐消息封面图
     *
     * @param string $mediaId
     *
     * @return Music
     */
    public function thumb($mediaId)
    {
        $this->setAttribute('thumb_media_id', $mediaId);

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
                'music'   => array(
                              'title'          => $this->title,
                              'description'    => $this->description,
                              'musicurl'       => $this->url,
                              'hqmusicurl'     => $this->hq_url,
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
                    'music' => array(
                                'Title'        => $this->title,
                                'Description'  => $this->description,
                                'MusicUrl'     => $this->url,
                                'HQMusicUrl'   => $this->hq_url,
                                'ThumbMediaId' => $this->thumb_media_id,
                               ),
                   );

        return $response;
    }
}
