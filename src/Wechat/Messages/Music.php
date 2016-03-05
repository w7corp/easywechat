<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Music.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat\Messages;

/**
 * 音乐消息.
 *
 * @property string $url
 * @property string $hq_url
 * @property string $title
 * @property string $description
 * @property string $thumb_media_id
 */
class Music extends BaseMessage
{
    /**
     * 属性.
     *
     * @var array
     */
    protected $properties = array(
                             'url',
                             'hq_url',
                             'title',
                             'description',
                             'thumb_media_id',
                            );

    /**
     * 设置音乐消息封面图.
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
     * 生成主动消息数组.
     *
     * @return array
     */
    public function toStaff()
    {
        return array(
                'music' => array(
                            'title' => $this->title,
                            'description' => $this->description,
                            'musicurl' => $this->url,
                            'hqmusicurl' => $this->hq_url,
                            'thumb_media_id' => $this->thumb_media_id,
                           ),
               );
    }

    /**
     * 生成回复消息数组.
     *
     * @return array
     */
    public function toReply()
    {
        $response = array(
                     'Music' => array(
                                 'Title' => $this->title,
                                 'Description' => $this->description,
                                 'MusicUrl' => $this->url,
                                 'HQMusicUrl' => $this->hq_url,
                                 'ThumbMediaId' => $this->thumb_media_id,
                                ),
                    );

        return $response;
    }
}
