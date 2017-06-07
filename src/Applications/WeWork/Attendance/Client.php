<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\Attendance;

use EasyWeChat\Applications\Base\Core\AbstractAPI;

/**
 * This is WeWork Attendance Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends AbstractAPI
{
    /**
     * Get the checkin data.
     *
     * @param int   $start
     * @param int   $end
     * @param array $userList
     * @param int   $type
     *
     * @return mixed
     */
    public function getCheckinData(int $start, int $end, array $userList, int $type = 3)
    {
        $params = [
            'opencheckindatatype' => $type,
            'starttime' => $start,
            'endtime' => $end,
            'useridlist' => $userList,
        ];

        return $this->parseJSON('json', ['https://qyapi.weixin.qq.com/cgi-bin/checkin/getcheckindata', $params]);
    }
}
