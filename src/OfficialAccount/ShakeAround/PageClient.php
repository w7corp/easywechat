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
     * Add a page.
     *
     * @param string $title
     * @param string $description
     * @param string $pageUrl
     * @param string $iconUrl
     * @param string $comment
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function create($title, $description, $pageUrl, $iconUrl, $comment = '')
    {
        $params = [
            'title' => $title,
            'description' => $description,
            'page_url' => $pageUrl,
            'icon_url' => $iconUrl,
        ];
        if ($comment !== '') {
            $params['comment'] = $comment;
        }

        return $this->httpPostJson('shakearound/page/add', $params);
    }

    /**
     * update a page info.
     *
     * @param int    $pageId
     * @param string $title
     * @param string $description
     * @param string $pageUrl
     * @param string $iconUrl
     * @param string $comment
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function update($pageId, $title, $description, $pageUrl, $iconUrl, $comment = '')
    {
        $params = [
            'page_id' => intval($pageId),
            'title' => $title,
            'description' => $description,
            'page_url' => $pageUrl,
            'icon_url' => $iconUrl,
        ];
        if ($comment !== '') {
            $params['comment'] = $comment;
        }

        return $this->httpPostJson('shakearound/page/update', $params);
    }

    /**
     * Fetch batch of pages by pageIds.
     *
     * @param array $pageIds
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function getByIds(array $pageIds)
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
    public function paginate($begin, $count)
    {
        $params = [
            'type' => 2,
            'begin' => intval($begin),
            'count' => intval($count),
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
    public function delete($pageId)
    {
        $params = [
            'page_id' => intval($pageId),
        ];

        return $this->httpPostJson('shakearound/page/delete', $params);
    }
}
