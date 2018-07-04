<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Message;

class MiniProgramPage extends AbstractMessage
{
    protected $type = 'miniprogrampage';

    protected $properties = [
        'title',
        'appid',
        'pagepath',
        'thumb_media_id',
    ];

    /**
     * 小程序卡片图片的媒体ID，小程序卡片图片建议大小为520*416
     * mediaId需后台通过api上传后获取.
     *
     * @param string $mediaId
     *
     * @return $this
     */
    public function thumb($mediaId)
    {
        $this->setAttribute('thumb_media_id', $mediaId);

        return $this;
    }
}
