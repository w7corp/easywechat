<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\OA;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Get the checkin data.
     *
     * @param int   $startTime
     * @param int   $endTime
     * @param array $userList
     * @param int   $type
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkinRecords(int $startTime, int $endTime, array $userList, int $type = 3)
    {
        $params = [
            'opencheckindatatype' => $type,
            'starttime' => $startTime,
            'endtime' => $endTime,
            'useridlist' => $userList,
        ];

        return $this->httpPostJson('cgi-bin/checkin/getcheckindata', $params);
    }

    /**
     * Get the checkin rules.
     *
     * @param int   $datetime
     * @param array $userList
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkinRules(int $datetime, array $userList)
    {
        $params = [
            'datetime' => $datetime,
            'useridlist' => $userList,
        ];

        return $this->httpPostJson('cgi-bin/checkin/getcheckinoption', $params);
    }

    /**
     * 获取企业所有打卡规则.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93384
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function corpCheckinRules()
    {
        return $this->httpPostJson('cgi-bin/checkin/getcorpcheckinoption');
    }

    /**
     * 获取打卡日报数据.
     *
     * @sse https://open.work.weixin.qq.com/api/doc/90000/90135/93374
     *
     * @param int   $startTime
     * @param int   $endTime
     * @param array $userids
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function checkinDayData(int $startTime, int $endTime, array $userids)
    {
        $params = [
            'starttime' => $startTime,
            'endtime' => $endTime,
            'useridlist' => $userids,
        ];

        return $this->httpPostJson('cgi-bin/checkin/getcheckin_daydata', $params);
    }

    /**
     * 获取打卡日报数据.
     *
     * @sse https://open.work.weixin.qq.com/api/doc/90000/90135/93387
     *
     * @param int   $startTime
     * @param int   $endTime
     * @param array $userids
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function checkinMonthData(int $startTime, int $endTime, array $userids)
    {
        $params = [
            'starttime' => $startTime,
            'endtime' => $endTime,
            'useridlist' => $userids,
        ];

        return $this->httpPostJson('cgi-bin/checkin/getcheckin_monthdata', $params);
    }

    /**
     * 获取打卡人员排班信息.
     *
     * @sse https://open.work.weixin.qq.com/api/doc/90000/90135/93380
     *
     * @param int   $startTime
     * @param int   $endTime
     * @param array $userids
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function checkinSchedus(int $startTime, int $endTime, array $userids)
    {
        $params = [
            'starttime' => $startTime,
            'endtime' => $endTime,
            'useridlist' => $userids,
        ];

        return $this->httpPostJson('cgi-bin/checkin/getcheckinschedulist', $params);
    }

    /**
     * 为打卡人员排班.
     *
     * @sse https://open.work.weixin.qq.com/api/doc/90000/90135/93385
     *
     * @param array $params
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function setCheckinSchedus(array $params)
    {
        return $this->httpPostJson('cgi-bin/checkin/setcheckinschedulist', $params);
    }

    /**
     * 录入打卡人员人脸信息.
     *
     * @sse https://open.work.weixin.qq.com/api/doc/90000/90135/93378
     *
     * @param string $userid
     * @param string $userface
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function addCheckinUserface(string $userid, string $userface)
    {
        $params = [
            'userid' => $userid,
            'userface' => $userface
        ];

        return $this->httpPostJson('cgi-bin/checkin/addcheckinuserface', $params);
    }

    /**
     * Get approval template details.
     *
     * @param string $templateId
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function approvalTemplate(string $templateId)
    {
        $params = [
            'template_id' => $templateId,
        ];

        return $this->httpPostJson('cgi-bin/oa/gettemplatedetail', $params);
    }

    /**
     * Submit an application for approval.
     *
     * @param array $data
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createApproval(array $data)
    {
        return $this->httpPostJson('cgi-bin/oa/applyevent', $data);
    }

    /**
     * Get Approval number.
     *
     * @param int   $startTime
     * @param int   $endTime
     * @param int   $nextCursor
     * @param int   $size
     * @param array $filters
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function approvalNumbers(int $startTime, int $endTime, int $nextCursor = 0, int $size = 100, array $filters = [])
    {
        $params = [
            'starttime' => $startTime,
            'endtime' => $endTime,
            'cursor' => $nextCursor,
            'size' => $size > 100 ? 100 : $size,
            'filters' => $filters,
        ];

        return $this->httpPostJson('cgi-bin/oa/getapprovalinfo', $params);
    }

    /**
     * Get approval detail.
     *
     * @param int $number
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function approvalDetail(int $number)
    {
        $params = [
            'sp_no' => $number,
        ];

        return $this->httpPostJson('cgi-bin/oa/getapprovaldetail', $params);
    }

    /**
     * Get Approval Data.
     *
     * @param int $startTime
     * @param int $endTime
     * @param int $nextNumber
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function approvalRecords(int $startTime, int $endTime, int $nextNumber = null)
    {
        $params = [
            'starttime' => $startTime,
            'endtime' => $endTime,
            'next_spnum' => $nextNumber,
        ];

        return $this->httpPostJson('cgi-bin/corp/getapprovaldata', $params);
    }


    /**
     * 获取公费电话拨打记录.
     *
     * @param int $startTime
     * @param int $endTime
     * @param int $offset
     * @param int $limit
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function dialRecords(int $startTime, int $endTime, int $offset = 0, $limit = 100)
    {
        $params = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'offset' => $offset,
            'limit' => $limit
        ];

        return $this->httpPostJson('cgi-bin/dial/get_dial_record', $params);
    }
}
