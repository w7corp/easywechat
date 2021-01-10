<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Live;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Get Room List.
     *
     * @param int $start
     * @param int $limit
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @deprecated This method has been merged into `\EasyWeChat\MiniProgram\Broadcast`
     */
    public function getRooms(int $start = 0, int $limit = 10)
    {
        $params = [
            'start' => $start,
            'limit' => $limit,
        ];

        return $this->httpPostJson('wxa/business/getliveinfo', $params);
    }

    /**
     * Get Playback List.
     *
     * @param int $roomId
     * @param int $start
     * @param int $limit
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @deprecated This method has been merged into `\EasyWeChat\MiniProgram\Broadcast`
     */
    public function getPlaybacks(int $roomId, int $start = 0, int $limit = 10)
    {
        $params = [
            'action' => 'get_replay',
            'room_id' => $roomId,
            'start' => $start,
            'limit' => $limit,
        ];

        return $this->httpPostJson('wxa/business/getliveinfo', $params);
    }
}
