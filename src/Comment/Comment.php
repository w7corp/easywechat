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
 * Comment.php.
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

namespace EasyWeChat\Comment;

use EasyWeChat\Core\AbstractAPI;

class Comment extends AbstractAPI
{
    const API_OPEN_COMMENT = 'https://api.weixin.qq.com/cgi-bin/comment/open';
    const API_CLOSE_COMMENT = 'https://api.weixin.qq.com/cgi-bin/comment/close';
    const API_LIST_COMMENT = 'https://api.weixin.qq.com/cgi-bin/comment/list';
    const API_MARK_ELECT = 'https://api.weixin.qq.com/cgi-bin/comment/markelect';
    const API_UNMARK_ELECT = 'https://api.weixin.qq.com/cgi-bin/comment/unmarkelect';
    const API_DELETE_COMMENT = 'https://api.weixin.qq.com/cgi-bin/comment/delete';
    const API_REPLY_COMMENT = 'https://api.weixin.qq.com/cgi-bin/comment/reply/add';
    const API_DELETE_REPLY = 'https://api.weixin.qq.com/cgi-bin/comment/reply/delete';

    /**
     * Open article comment.
     *
     * @param string $msgId
     * @param int    $index
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function open($msgId, $index)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
        ];

        return $this->parseJSON('json', [self::API_OPEN_COMMENT, $params]);
    }

    /**
     * Close comment.
     *
     * @param string $msgId
     * @param int    $index
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function close($msgId, $index)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
        ];

        return $this->parseJSON('json', [self::API_CLOSE_COMMENT, $params]);
    }

    /**
     * Get article comments.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $begin
     * @param int    $count
     * @param int    $type
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function lists($msgId, $index, $begin, $count, $type = 0)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'begin' => $begin,
            'count' => $count,
            'type' => $type,
        ];

        return $this->parseJSON('json', [self::API_LIST_COMMENT, $params]);
    }

    /**
     * Mark elect comment.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function markElect($msgId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->parseJSON('json', [self::API_MARK_ELECT, $params]);
    }

    /**
     * Unmark elect comment.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function unmarkElect($msgId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->parseJSON('json', [self::API_UNMARK_ELECT, $params]);
    }

    /**
     * Delete comment.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function delete($msgId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->parseJSON('json', [self::API_DELETE_COMMENT, $params]);
    }

    /**
     * Reply to a comment.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $commentId
     * @param string $content
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function reply($msgId, $index, $commentId, $content)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'user_comment_id' => $commentId,
            'content' => $content,
        ];

        return $this->parseJSON('json', [self::API_REPLY_COMMENT, $params]);
    }

    /**
     * Delete a reply.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function deleteReply($msgId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->parseJSON('json', [self::API_DELETE_REPLY, $params]);
    }
}
