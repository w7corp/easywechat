<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\ShakeAround;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class PageClient.
 *
 * @author allen05ren <allen05ren@outlook.com>
 */
class PageClient extends BaseClient
{
    /**
     * @param array $data
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function create(array $data)
    {
        return $this->httpPostJson('shakearound/page/add', $data);
    }

    /**
     * @param int   $pageId
     * @param array $data
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function update(int $pageId, array $data)
    {
        return $this->httpPostJson('shakearound/page/update', array_merge(['page_id' => $pageId], $data));
    }

    /**
     * Fetch batch of pages by pageIds.
     *
     * @param array $pageIds
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function listByIds(array $pageIds)
    {
        $params = [
            'type' => 1,
            'page_ids' => $pageIds,
        ];

        return $this->httpPostJson('shakearound/page/search', $params);
    }

    /**
     * Pagination to get batch of pages.
     *
     * @param int $begin
     * @param int $count
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function list(int $begin, int $count)
    {
        $params = [
            'type' => 2,
            'begin' => $begin,
            'count' => $count,
        ];

        return $this->httpPostJson('shakearound/page/search', $params);
    }

    /**
     * delete a page.
     *
     * @param int $pageId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete(int $pageId)
    {
        $params = [
            'page_id' => $pageId,
        ];

        return $this->httpPostJson('shakearound/page/delete', $params);
    }
}
