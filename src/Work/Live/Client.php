<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Live;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * 获取成员直播ID列表
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92735
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserLivingId(string $userId, int $beginTime, int $endTime, string $nextKey = '0', int $limit = 100)
    {
        $params = [
            'userid' => $userId,
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'next_key' => $nextKey,
            'limit' => $limit
        ];

        return $this->httpPostJson('cgi-bin/living/get_user_livingid', $params);
    }

    /**
     * 获取直播详情
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92734
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLiving(string $livingId)
    {
        $params = [
            'livingid' => $livingId,
        ];

        return $this->httpGet('cgi-bin/living/get_living_info', $params);
    }

    /**
     * 获取看直播统计
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92736
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWatchStat(string $livingId, string $nextKey = '0')
    {
        $params = [
            'livingid' => $livingId,
            'next_key' => $nextKey,
        ];

        return $this->httpPostJson('cgi-bin/living/get_watch_stat', $params);
    }
}
