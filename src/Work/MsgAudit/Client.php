<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\MsgAudit;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author ZengJJ <z373522886@foxmail.com >
 */
class Client extends BaseClient
{
    /**
     * @param string|null $type
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getPermitUsers(string $type = null)
    {
        return $this->httpPostJson('cgi-bin/msgaudit/get_permit_user_list', (empty($type) ? [] : ['type' => $type]));
    }

    /**
     * @param array $info 数组，格式: [[userid, exteranalopenid], [userid, exteranalopenid]]
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getSingleAgreeStatus(array $info)
    {
        $params = [
            'info' => $info
        ];

        return $this->httpPostJson('cgi-bin/msgaudit/check_single_agree', $params);
    }

    /**
     * @param string $roomid
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getRoomAgreeStatus(string $roomId)
    {
        $params = [
            'roomid' => $roomId
        ];

        return $this->httpPostJson('cgi-bin/msgaudit/check_room_agree', $params);
    }

    /**
     * @param string $roomid
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getRoom(string $roomId)
    {
        $params = [
            'roomid' => $roomId
        ];

        return $this->httpPostJson('cgi-bin/msgaudit/groupchat/get', $params);
    }
}
