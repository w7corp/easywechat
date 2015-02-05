<?php

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;
use Overtrue\Wechat\Utils\XML;

class Voice extends AbstractMessage implements MessageInterface
{

    protected $properties = array('media_id');

    /**
     * 设置语音
     *
     * @param string $path
     *
     * @return Overtrue\Wechat\Messages\Voice
     */
    public function voice($path)
    {
        $this->attributes['media_id'] = Media::voice($path);

        return $this;
    }

    public function formatToClient()
    {
        return array(
                'touser'  => $this->to,
                'msgtype' => 'voice',
                'voice'   => array(
                              'media_id' => $this->media_id
                             ),
               );
    }

    public function formatToServer()
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