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
     * @param array $deviceIdentifier
     * @param array $pageIds
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function bindPage(array $deviceIdentifier, array $pageIds)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'page_ids' => $pageIds,
        ];

        return $this->parseJSON('json', [self::API_DEVICE_BINDPAGE, $params]);
    }

    /**
     * Get pageIds by deviceId.
     *
     * @param array $deviceIdentifier
     * @param bool  $raw
     *
     * @return array|\EasyWeChat\Support\Collection
     */
    public function getPageByDeviceId(array $deviceIdentifier, $raw = false)
    {
        $params = [
            'type' => 1,
            'device_identifier' => $deviceIdentifier,
        ];

        $result = $this->parseJSON('json', [self::API_RELATION_SEARCH, $params]);

        if (true === $raw) {
            return $result;
        }
        $page_ids = [];
        if (!empty($result->data['relations'])) {
            foreach ($result->data['relations'] as $item) {
                $page_ids[] = $item['page_id'];
            }
        }

        return $page_ids;
    }

    /**
     * Get devices by pageId.
     *
     * @param int $pageId
     * @param int $begin
     * @param int $count
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getDeviceByPageId($pageId, $begin, $count)
    {
        $params = [
            'type' => 2,
            'page_id' => intval($pageId),
            'begin' => intval($begin),
            'count' => intval($count),
        ];

        return $this->parseJSON('json', [self::API_RELATION_SEARCH, $params]);
    }
}
