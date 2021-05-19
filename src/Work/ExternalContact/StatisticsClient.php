<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\ExternalContact;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class StatisticsClient.
 *
 * @author 读心印 <aa24615@qq.com>
 */
class StatisticsClient extends BaseClient
{
    /**
     * 获取「联系客户统计」数据.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92132
     *
     * @param array  $userIds
     * @param string $from
     * @param string $to
     * @param array  $partyIds
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function userBehavior(array $userIds, string $from, string $to, array $partyIds = [])
    {
        $params = [
            'userid' => $userIds,
            'partyid' => $partyIds,
            'start_time' => $from,
            'end_time' => $to,
        ];
        return $this->httpPostJson('cgi-bin/externalcontact/get_user_behavior_data', $params);
    }


    /**
     * 获取「群聊数据统计」数据. (按群主聚合的方式)
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92133
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function groupChatStatistic(array $params)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/groupchat/statistic', $params);
    }


    /**
     * 获取「群聊数据统计」数据. (按自然日聚合的方式)
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92133
     *
     * @param int $dayBeginTime
     * @param int $dayEndTime
     * @param array $userIds
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function groupChatStatisticGroupByDay(int $dayBeginTime, int $dayEndTime, array $userIds = [])
    {
        $params = [
            'day_begin_time' => $dayBeginTime,
            'day_end_time' => $dayEndTime,
            'owner_filter' => [
                'userid_list' => $userIds
            ]
        ];
        return $this->httpPostJson('cgi-bin/externalcontact/groupchat/statistic_group_by_day', $params);
    }
}
