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
 * Trait ManageComments.php.
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

trait ManageComments
{
    /**
     * Open article comment.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function openComment()
    {
        return $this->parseJSON('post', [self::API_OPEN_COMMENT, $this->mediaPress()]);
    }

    /**
     * Close comment.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function closeComment()
    {
        return $this->parseJSON('post', [self::API_CLOSE_COMMENT, $this->mediaPress()]);
    }

    /**
     * Get article comments.
     *
     * @param int $begin
     * @param int $count
     * @param int $type
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function comments($begin, $count, $type = 0)
    {
        $params = array_merge($this->mediaPress(), [
            'begin' => $begin,
            'count' => $count,
            'type' => $type,
        ]);

        return $this->parseJSON('post', [self::API_LIST_COMMENT, $params]);
    }

    /**
     * Mark elect comment.
     *
     * @param int $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function markElectComment($commentId)
    {
        $params = array_merge($this->mediaPress(), [
            'user_comment_id' => $commentId,
        ]);

        return $this->parseJSON('post', [self::API_MARK_ELECT, $params]);
    }

    /**
     * Unmark comment elect.
     *
     * @param int $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function unmarkElectComment($commentId)
    {
        $params = array_merge($this->mediaPress(), [
            'user_comment_id' => $commentId,
        ]);

        return $this->parseJSON('post', [self::API_UNMARK_ELECT, $params]);
    }

    /**
     * Delete comment.
     *
     * @param int $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function deleteComment($commentId)
    {
        $params = array_merge($this->mediaPress(), [
            'user_comment_id' => $commentId,
        ]);

        return $this->parseJSON('post', [self::API_DELETE_COMMENT, $params]);
    }
}
