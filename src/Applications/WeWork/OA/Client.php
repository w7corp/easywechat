<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\OA;

use EasyWeChat\Support\HasHttpRequests;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client
{
    use HasHttpRequests;

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
    public function getCheckinData(int $startTime, int $endTime, array $userList, int $type = 3)
    {
        $params = [
            'opencheckindatatype' => $type,
            'starttime' => $startTime,
            'endtime' => $endTime,
            'useridlist' => $userList,
        ];

        return $this->parseJSON($this->postJson('checkin/getcheckindata', $params));
    }

    /**
     * Get Approval Data.
     *
     * @param int $startTime
     * @param int $endTime
     * @param int $nextNumber
     *
     * @return mixed
     */
    public function getApprovalData(int $startTime, int $endTime, $nextNumber)
    {
        $params = [
            'starttime' => $startTime,
            'endtime' => $endTime,
            'next_spnum' => $nextNumber,
        ];

        return $this->parseJSON($this->postJson('corp/getapprovaldata', $params));
    }
}
