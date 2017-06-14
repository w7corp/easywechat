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

use EasyWeChat\Support\HasHttpRequests;

/**
 * Class TagClient.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class TagClient
{
    use HasHttpRequests {
        get as httpGet;
    }

    public function create(string $tagName, int $tagId = null)
    {
        return $this->parseJSON($this->post('https://qyapi.weixin.qq.com/cgi-bin/tag/create', ['tagname' => $tagName, 'tagid' => $tagId]));
    }

    public function update(int $tagId, string $tagName)
    {
        return $this->parseJSON($this->post('https://qyapi.weixin.qq.com/cgi-bin/tag/update', ['tagid' => $tagId, 'tagname' => $tagName]));
    }

    public function delete(int $tagId)
    {
        return $this->parseJSON($this->httpGet('https://qyapi.weixin.qq.com/cgi-bin/tag/delete', ['tagid' => $tagId]));
    }

    public function get(int $tagId)
    {
        return $this->parseJSON($this->httpGet('https://qyapi.weixin.qq.com/cgi-bin/tag/get', ['tagid' => $tagId]));
    }

    public function addUsers(int $tagId, array $userList = [], array $partyList = [])
    {
        return $this->addOrDeleteUsers('https://qyapi.weixin.qq.com/cgi-bin/tag/addtagusers', $tagId, $userList, $partyList);
    }

    public function deleteUsers(int $tagId, array $userList = [], array $partyList = [])
    {
        return $this->addOrDeleteUsers('https://qyapi.weixin.qq.com/cgi-bin/tag/deltagusers', $tagId, $userList, $partyList);
    }

    protected function addOrDeleteUsers($endpoint, string $action, int $tagId, array $userList = [], array $partyList = [])
    {
        $data = [
            'tagid' => $tagId,
            'userlist' => $userList,
            'partylist' => $partyList,
        ];

        return $this->parseJSON($this->post($endpoint, $data));
    }

    public function lists()
    {
        return $this->parseJSON($this->httpGet('https://qyapi.weixin.qq.com/cgi-bin/tag/list'));
    }
}
