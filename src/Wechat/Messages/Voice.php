<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;
use Overtrue\Wechat\Utils\XML;

/**
 * @property string $media_id
 */
class Voice extends AbstractMessage
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
     * @see OvertrueWechatMessagesAbstractMessage::buildForStaff();
     */
    public function buildForStaff)
    {
        return array(
                'touser'  => $this->to,
                'msgtype' => 'voice',
                'voice'   => array(
                              'media_id' => $this->media_id,
                             ),
               );
    }

    /**
     * @see OvertrueWechatMessagesAbstractMessage::buildForReply();
     */
    public function buildForReply)
    {
        $response = array(
                     'ToUserName'   => $this->to,
                     'FromUserName' => $this->from,
                     'CreateTime'   => time(),
                     'MsgType'      => 'voice',
                     'Voice'        => array(
                                        'MediaId' => $this->media_id,
                                       ),
                    );

        return XML::build($response);
    }

}