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
 * MediaPress.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2017
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\MediaPress;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;

class MediaPress extends AbstractAPI
{
    use ManageComments, ManageCommentReplies;

    const API_OPEN_COMMENT = 'https://api.weixin.qq.com/cgi-bin/comment/open';
    const API_CLOSE_COMMENT = 'https://api.weixin.qq.com/cgi-bin/comment/close';
    const API_LIST_COMMENT = 'https://api.weixin.qq.com/cgi-bin/comment/list';
    const API_MARK_ELECT = 'https://api.weixin.qq.com/cgi-bin/comment/markelect';
    const API_UNMARK_ELECT = 'https://api.weixin.qq.com/cgi-bin/comment/unmarkelect';
    const API_DELETE_COMMENT = 'https://api.weixin.qq.com/cgi-bin/comment/delete';
    const API_REPLY_COMMENT = 'https://api.weixin.qq.com/cgi-bin/comment/reply/add';
    const API_DELETE_REPLY = 'https://api.weixin.qq.com/cgi-bin/comment/reply/delete';

    /**
     * @var array
     */
    protected $mediaPress;

    /**
     * Select an article.
     *
     * @param int $dataId
     * @param int $index
     *
     * @return $this
     */
    public function select($dataId, $index = null)
    {
        $this->mediaPress = [
            'msg_data_id' => $dataId,
            'index' => $index,
        ];

        return $this;
    }

    /**
     * Return the media-press.
     *
     * @return array
     *
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function mediaPress()
    {
        if ($this->mediaPress) {
            return $this->mediaPress;
        }

        throw new InvalidArgumentException('Missing media-press data.');
    }
}
