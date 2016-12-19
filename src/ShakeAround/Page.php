<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Page.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue
 * @see       http://overtrue.me
 */

namespace EasyWeChat\ShakeAround;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class Page.
 */
class Page extends AbstractAPI
{
    const API_ADD = 'https://api.weixin.qq.com/shakearound/page/add';
    const API_UPDATE = 'https://api.weixin.qq.com/shakearound/page/update';
    const API_SEARCH = 'https://api.weixin.qq.com/shakearound/page/search';
    const API_DELETE = 'https://api.weixin.qq.com/shakearound/page/delete';
    const API_RELATION_SEARCH = 'https://api.weixin.qq.com/shakearound/relation/search';

    /**
     * Add a page.
     *
     * @param string $title
     * @param string $description
     * @param string $pageUrl
     * @param string $iconUrl
     * @param string $comment
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function add($title, $description, $pageUrl, $iconUrl, $comment = '')
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

        return $this->parseJSON('json', [self::API_ADD, $params]);
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
     * @return \EasyWeChat\Support\Collection
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

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    /**
     * Fetch batch of pages by pageIds.
     *
     * @param array $pageIds
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function fetchByIds(array $pageIds)
    {
        $params = [
            'type' => 1,
            'page_ids' => $pageIds,
        ];

        return $this->parseJSON('json', [self::API_SEARCH, $params]);
    }

    /**
     * Pagination to fetch batch of pages.
     *
     * @param int $begin
     * @param int $count
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function pagination($begin, $count)
    {
        $params = [
            'type' => 2,
            'begin' => intval($begin),
            'count' => intval($count),
        ];

        return $this->parseJSON('json', [self::API_SEARCH, $params]);
    }

    /**
     * delete a page.
     *
     * @param int $pageId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function delete($pageId)
    {
        $params = [
            'page_id' => intval($pageId),
        ];

        return $this->parseJSON('json', [self::API_DELETE, $params]);
    }
}
