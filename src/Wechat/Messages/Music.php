<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;
use Overtrue\Wechat\Utils\XML;

/**
 * @property string $url
 * @property string $hq_url
 * @property string $title
 * @property string $description
 * @property string $thumb_media_id
 */
class Music extends AbstractMessage
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
     * @see Overtrue\Wechat\Messages\AbstractMessage::buildForStaff();
     */
    public function buildForStaff()
    {
        return array(
                'touser'  => $this->to,
                'msgtype' => 'music',
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
     * @see Overtrue\Wechat\Messages\AbstractMessage::buildForReply();
     */
    public function buildForReply()
    {
       $response = array(
                    'ToUserName'   => $this->to,
                    'FromUserName' => $this->from,
                    'CreateTime'   => time(),
                    'MsgType'      => 'music',
                    'music'        => array(
                                       'Title'        => $this->title,
                                       'Description'  => $this->description,
                                       'MusicUrl'     => $this->url,
                                       'HQMusicUrl'   => $this->hq_url,
                                       'ThumbMediaId' => $this->thumb_media_id,
                                      ),
                   );

        return XML::build($response);
    }

}