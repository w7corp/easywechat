<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Schedule;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author her-cat <i@her-cat.com>
 */
class Client extends BaseClient
{
    /**
     * Add a schedule.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(array $schedule)
    {
        return $this->httpPostJson('cgi-bin/oa/schedule/add', compact('schedule'));
    }

    /**
     * Update the schedule.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $id, array $schedule)
    {
        $schedule += ['schedule_id' => $id];

        return $this->httpPostJson('cgi-bin/oa/schedule/update', compact('schedule'));
    }

    /**
     * Get one or more schedules.
     *
     * @param string|array $ids
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($ids)
    {
        return $this->httpPostJson('cgi-bin/oa/schedule/get', ['schedule_id_list' => (array) $ids]);
    }

    /**
     * Get the list of schedules under a calendar.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getByCalendar(string $calendarId, int $offset = 0, int $limit = 500)
    {
        $data = compact('offset', 'limit') + ['cal_id' => $calendarId];

        return $this->httpPostJson('cgi-bin/oa/schedule/get_by_calendar', $data);
    }

    /**
     * Delete a schedule.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $id)
    {
        return $this->httpPostJson('cgi-bin/oa/schedule/del', ['schedule_id' => $id]);
    }
}
