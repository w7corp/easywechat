<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\User;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class TagClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class TagClient extends BaseClient
{
    /**
     * Create tag.
     *
     * @param string $name
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function create($name)
    {
        $params = [
            'tag' => ['name' => $name],
        ];

        return $this->httpPostJson('cgi-bin/tags/create', $params);
    }

    /**
     * List all tags.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function lists()
    {
        return $this->httpGet('cgi-bin/tags/get');
    }

    /**
     * Update a tag name.
     *
     * @param int    $tagId
     * @param string $name
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function update($tagId, $name)
    {
        $params = [
            'tag' => [
                'id' => $tagId,
                'name' => $name,
            ],
        ];

        return $this->httpPostJson('cgi-bin/tags/update', $params);
    }

    /**
     * Delete tag.
     *
     * @param int $tagId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete($tagId)
    {
        $params = [
            'tag' => ['id' => $tagId],
        ];

        return $this->httpPostJson('cgi-bin/tags/delete', $params);
    }

    /**
     * Get user tags.
     *
     * @param string $openId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function userTags($openId)
    {
        $params = ['openid' => $openId];

        return $this->httpPostJson('cgi-bin/tags/getidlist', $params);
    }

    /**
     * Get users from a tag.
     *
     * @param string $tagId
     * @param string $nextOpenId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function usersOfTag($tagId, $nextOpenId = '')
    {
        $params = [
            'tagid' => $tagId,
            'next_openid' => $nextOpenId,
        ];

        return $this->httpPostJson('cgi-bin/user/tag/get', $params);
    }

    /**
     * Batch tag users.
     *
     * @param array $openIds
     * @param int   $tagId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function batchTagUsers(array $openIds, $tagId)
    {
        $params = [
            'openid_list' => $openIds,
            'tagid' => $tagId,
        ];

        return $this->httpPostJson('cgi-bin/tags/members/batchtagging', $params);
    }

    /**
     * Untag users from a tag.
     *
     * @param array $openIds
     * @param int   $tagId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function batchUntagUsers(array $openIds, $tagId)
    {
        $params = [
            'openid_list' => $openIds,
            'tagid' => $tagId,
        ];

        return $this->httpPostJson('cgi-bin/tags/members/batchuntagging', $params);
    }
}
