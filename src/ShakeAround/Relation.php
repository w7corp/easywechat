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
 * Relation.php.
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
 * Class Relation.
 */
class Relation extends AbstractAPI
{
    const API_DEVICE_BINDPAGE = 'https://api.weixin.qq.com/shakearound/device/bindpage';
    const API_RELATION_SEARCH = 'https://api.weixin.qq.com/shakearound/relation/search';


    /**
     * Bind pages for device.
     *
     * @param array $device_identifier
     * @param array $page_ids
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function bindPage(array $device_identifier, array $page_ids)
    {
        $params = [
            'device_identifier' => $device_identifier,
            'page_ids' => $page_ids,
        ];

        return $this->parseJSON('json', [self::API_DEVICE_BINDPAGE, $params]);
    }

    /**
     * Get page_ids by device_id.
     *
     * @param array $device_identifier
     * @param bool  $raw
     *
     * @return array|\EasyWeChat\Support\Collection
     */
    public function getPageByDeviceId(array $device_identifier, $raw = false)
    {
        $params = [
            'type' => 1,
            'device_identifier' => $device_identifier,
        ];

        $result = $this->parseJSON('json', [self::API_RELATION_SEARCH, $params]);

        if ($raw === true) {
            return $result;
        }
        $page_ids = array();
        if (!empty($result->data['relations'])) {
            foreach ($result->data['relations'] as $item) {
                $page_ids[] = $item['page_id'];
            }
        }

        return $page_ids;
    }

    /**
     * Get devices by page_id.
     *
     * @param int $page_id
     * @param int $begin
     * @param int $count
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getDeviceByPageId($page_id, $begin, $count)
    {
        $params = [
            'type' => 2,
            'page_id' => intval($page_id),
            'begin' => intval($begin),
            'count' => intval($count),
        ];

        return $this->parseJSON('json', [self::API_RELATION_SEARCH, $params]);
    }
}
