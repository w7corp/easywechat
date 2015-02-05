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
class Music extends AbstractMessage implements MessageInterface
{

    protected $properties = array('url', 'hq_url', 'title', 'description');

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

    public function formatToClient()
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

    public function formatToServer()
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