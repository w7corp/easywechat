<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\WeWork\User;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class TagClient.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class TagClient extends BaseClient
{
    /**
     * Create tag.
     *
     * @param string $tagName
     * @param null   $tagId
     *
     * @return mixed
     */
    public function create(string $tagName, $tagId = null)
    {
        $params = [
            'tagname' => $tagName,
            'tagid' => $tagId,
        ];

        return $this->httpPostJson('tag/create', $params);
    }

    /**
     * Update tag.
     *
     * @param int    $tagId
     * @param string $tagName
     *
     * @return mixed
     */
    public function update($tagId, string $tagName)
    {
        $params = [
            'tagid' => $tagId,
            'tagname' => $tagName,
        ];

        return $this->httpPostJson('tag/update', $params);
    }

    /**
     * Delete tag.
     *
     * @param int $tagId
     *
     * @return mixed
     */
    public function delete(int $tagId)
    {
        return $this->httpGet('tag/delete', ['tagid' => $tagId]);
    }

    /**
     * @param int $tagId
     *
     * @return mixed
     */
    public function get($tagId)
    {
        return $this->httpGet('tag/get', ['tagid' => $tagId]);
    }

    /**
     * @param int   $tagId
     * @param array $userList
     * @param array $partyList
     *
     * @return mixed
     */
    public function addUsers($tagId, array $userList = [], array $partyList = [])
    {
        return $this->addOrDeleteUsers('tag/addtagusers', $tagId, $userList, $partyList);
    }

    /**
     * @param $tagId
     * @param array $userList
     * @param array $partyList
     *
     * @return mixed
     */
    public function deleteUsers($tagId, array $userList = [], array $partyList = [])
    {
        return $this->addOrDeleteUsers('tag/deltagusers', $tagId, $userList, $partyList);
    }

    /**
     * @param $endpoint
     * @param $tagId
     * @param array $userList
     * @param array $partyList
     *
     * @return mixed
     */
    protected function addOrDeleteUsers($endpoint, $tagId, array $userList, array $partyList)
    {
        $data = [
            'tagid' => $tagId,
            'userlist' => $userList,
            'partylist' => $partyList,
        ];

        return $this->httpPostJson($endpoint, $data);
    }

    /**
     * @return mixed
     */
    public function lists()
    {
        return $this->httpGet('tag/list');
    }
}
