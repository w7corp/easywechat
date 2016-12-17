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
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
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
     * @param string $page_url
     * @param string $icon_url
     * @param string $comment
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function add($title, $description, $page_url, $icon_url, $comment = '')
    {
        $params = [
            'title' => $title,
            'description' => $description,
            'page_url' => $page_url,
            'icon_url' => $icon_url,
        ];
        if ($comment !== '') {
            $params['comment'] = $comment;
        }

        return $this->parseJSON('json', [self::API_ADD, $params]);
    }

    /**
     * update a page info.
     *
     * @param int    $page_id
     * @param string $title
     * @param string $description
     * @param string $page_url
     * @param string $icon_url
     * @param string $comment
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function update($page_id, $title, $description, $page_url, $icon_url, $comment = '')
    {
        $params = [
            'page_id' => intval($page_id),
            'title' => $title,
            'description' => $description,
            'page_url' => $page_url,
            'icon_url' => $icon_url,
        ];
        if ($comment !== '') {
            $params['comment'] = $comment;
        }

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    /**
     * Fetch batch of pages by page_ids.
     *
     * @param array $page_ids
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function fetchByIds(array $page_ids)
    {
        $params = [
            'type' => 1,
            'page_ids' => $page_ids,
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
     * @param int $page_id
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function delete($page_id)
    {
        $params = [
            'page_id' => intval($page_id),
        ];

        return $this->parseJSON('json', [self::API_DELETE, $params]);
    }
}
