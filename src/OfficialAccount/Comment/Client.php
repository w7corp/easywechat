<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Comment;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * Open article comment.
     *
     * @param string $msgId
     * @param int    $index
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function open($msgId, $index)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
        ];

        return $this->httpPostJson('cgi-bin/comment/open', $params);
    }

    /**
     * Close comment.
     *
     * @param string $msgId
     * @param int    $index
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function close($msgId, $index)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
        ];

        return $this->httpPostJson('cgi-bin/comment/close', $params);
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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

        return $this->httpPostJson('cgi-bin/comment/list', $params);
    }

    /**
     * Mark elect comment.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function markElect($msgId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->httpPostJson('cgi-bin/comment/markelect', $params);
    }

    /**
     * Unmark elect comment.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function unmarkElect($msgId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->httpPostJson('cgi-bin/comment/unmarkelect', $params);
    }

    /**
     * Delete comment.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete($msgId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->httpPostJson('cgi-bin/comment/delete', $params);
    }

    /**
     * Reply to a comment.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $commentId
     * @param string $content
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function reply($msgId, $index, $commentId, $content)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'user_comment_id' => $commentId,
            'content' => $content,
        ];

        return $this->httpPostJson('cgi-bin/comment/reply/add', $params);
    }

    /**
     * Delete a reply.
     *
     * @param string $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function deleteReply($msgId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $msgId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->httpPostJson('cgi-bin/comment/reply/delete', $params);
    }
}
