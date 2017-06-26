<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OfficialAccount\Comment;

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
     * @param int $dataId
     * @param int $index
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function open($dataId, $index = null)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
        ];

        return $this->httpPost('cgi-bin/comment/open', $params);
    }

    /**
     * Close comment.
     *
     * @param int $dataId
     * @param int $index
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function close($dataId, $index = null)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
        ];

        return $this->httpPost('cgi-bin/comment/close', $params);
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
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

        return $this->httpPost('cgi-bin/comment/list', $params);
    }

    /**
     * Mark elect comment.
     *
     * @param int $dataId
     * @param int $index
     * @param int $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function markElect($dataId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->httpPost('cgi-bin/comment/markelect', $params);
    }

    /**
     * Unmark elect comment.
     *
     * @param int $dataId
     * @param int $index
     * @param int $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function unmarkElect($dataId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->httpPost('cgi-bin/comment/unmarkelect', $params);
    }

    /**
     * Delete comment.
     *
     * @param int $dataId
     * @param int $index
     * @param int $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function delete($dataId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->httpPost('cgi-bin/comment/delete', $params);
    }

    /**
     * Reply to a comment.
     *
     * @param int    $dataId
     * @param int    $index
     * @param int    $commentId
     * @param string $content
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function reply($dataId, $index, $commentId, $content)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'user_comment_id' => $commentId,
            'content' => $content,
        ];

        return $this->httpPost('cgi-bin/comment/reply/add', $params);
    }

    /**
     * Delete a reply.
     *
     * @param int $dataId
     * @param int $index
     * @param int $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function deleteReply($dataId, $index, $commentId)
    {
        $params = [
            'msg_data_id' => $dataId,
            'index' => $index,
            'user_comment_id' => $commentId,
        ];

        return $this->httpPost('cgi-bin/comment/reply/delete', $params);
    }
}
