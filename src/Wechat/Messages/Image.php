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
 * Image.php.
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
 * 图片消息.
 *
 * @property string $media_id
 */
class Image extends BaseMessage
{
    /**
     * 属性.
     *
     * @var array
     */
    protected $properties = array('media_id');

    /**
     * 设置音乐消息封面图.
     *
     * @param string $mediaId
     *
     * @return Image
     */
    public function media($mediaId)
    {
        $this->setAttribute('media_id', $mediaId);

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
                'image' => array(
                            'media_id' => $this->media_id,
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
        return array(
                'Image' => array(
                            'MediaId' => $this->media_id,
                           ),
               );
    }
}
