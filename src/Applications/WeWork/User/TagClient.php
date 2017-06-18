<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\User;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class TagClient.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class TagClient extends BaseClient
{
    public function create(string $tagName, int $tagId = null)
    {
        return $this->post('tag/create', ['tagname' => $tagName, 'tagid' => $tagId]);
    }

    public function update(int $tagId, string $tagName)
    {
        return $this->post('tag/update', ['tagid' => $tagId, 'tagname' => $tagName]);
    }

    public function delete(int $tagId)
    {
        return parent::get('tag/delete', ['tagid' => $tagId]);
    }

    public function get(int $tagId)
    {
        return parent::get('tag/get', ['tagid' => $tagId]);
    }

    public function addUsers(int $tagId, array $userList = [], array $partyList = [])
    {
        return $this->addOrDeleteUsers('tag/addtagusers', $tagId, $userList, $partyList);
    }

    public function deleteUsers(int $tagId, array $userList = [], array $partyList = [])
    {
        return $this->addOrDeleteUsers('tag/deltagusers', $tagId, $userList, $partyList);
    }

    protected function addOrDeleteUsers($endpoint, int $tagId, array $userList = [], array $partyList = [])
    {
        $data = [
            'tagid' => $tagId,
            'userlist' => $userList,
            'partylist' => $partyList,
        ];

        return $this->post($endpoint, $data);
    }

    public function lists()
    {
        return parent::get('tag/list');
    }
}
