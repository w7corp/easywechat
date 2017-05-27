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
 * Trait ManageCommentReplies.php.
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

trait ManageCommentReplies
{
    /**
     * Reply to a comment.
     *
     * @param int    $commentId
     * @param string $content
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function replyComment($commentId, $content)
    {
        $params = array_merge($this->mediaPress(), [
            'user_comment_id' => $commentId,
            'content' => $content,
        ]);

        return $this->parseJSON('post', [self::API_REPLY_COMMENT, $params]);
    }

    /**
     * Delete a reply.
     *
     * @param int $commentId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function deleteCommentReply($commentId)
    {
        $params = array_merge($this->mediaPress(), [
            'user_comment_id' => $commentId,
        ]);

        return $this->parseJSON('post', [self::API_DELETE_REPLY, $params]);
    }
}
