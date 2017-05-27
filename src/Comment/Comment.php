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
     * @param int $dataId
     * @param int $index
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function open($dataId, $index = null)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
        ];

        return $this->parseJSON('post', [self::API_OPEN_COMMENT, $params]);
    }

    /**
     * Close comment.
     *
     * @param int $dataId
     * @param int $index
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function close($dataId, $index = null)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
        ];

        return $this->parseJSON('post', [self::API_CLOSE_COMMENT, $params]);
    }

    /**
     * Get article comments.
     *
     * @param int $dataId
     * @param int $index
     * @param int $begin
     * @param int $count
     * @param int $type
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function lists($dataId, $index, $begin, $count, $type = 0)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'begin' => $begin,
            'count' => $count,
            'type' => $type,
        ];

        return $this->parseJSON('post', [self::API_LIST_COMMENT, $params]);
    }

    /**
     * Mark elect comment.
     *
     * @param int $dataId
     * @param int $index
     * @param int $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function markElect($dataId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->parseJSON('post', [self::API_MARK_ELECT, $params]);
    }

    /**
     * Unmark elect comment.
     *
     * @param int $dataId
     * @param int $index
     * @param int $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function unmarkElect($dataId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->parseJSON('post', [self::API_UNMARK_ELECT, $params]);
    }

    /**
     * Delete comment.
     *
     * @param int $dataId
     * @param int $index
     * @param int $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function delete($dataId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->parseJSON('post', [self::API_DELETE_COMMENT, $params]);
    }

    /**
     * Reply to a comment.
     *
     * @param int    $dataId
     * @param int    $index
     * @param int    $commentId
     * @param string $content
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function reply($dataId, $index, $commentId, $content)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'user_comment_id' => $commentId,
            'content' => $content,
        ];

        return $this->parseJSON('post', [self::API_REPLY_COMMENT, $params]);
    }

    /**
     * Delete a reply.
     *
     * @param int $dataId
     * @param int $index
     * @param int $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function deleteReply($dataId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->parseJSON('post', [self::API_DELETE_REPLY, $params]);
    }
}
