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
 * Shelf.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a939638621 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 *
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Shop;

use Overtrue\Wechat\Shop\Data\Shelf as ShelfData;
use Overtrue\Wechat\Shop\Foundation\Base;
use Overtrue\Wechat\Shop\Foundation\Shelf as ShelfInterface;
use Overtrue\Wechat\Shop\Foundation\ShopsException;

/**
 * 货架系统
 *
 * Class Shelf
 */
class Shelf extends Base implements ShelfInterface
{
    const API_ADD = 'https://api.weixin.qq.com/merchant/shelf/add';
    const API_DELETE = 'https://api.weixin.qq.com/merchant/shelf/del';
    const API_UPDATE = 'https://api.weixin.qq.com/merchant/shelf/mod';
    const API_LISTS = 'https://api.weixin.qq.com/merchant/shelf/getall';
    const API_GET_BY_ID = 'https://api.weixin.qq.com/merchant/shelf/getbyid';

    /**
     * 添加货架.
     *
     * @param $shelfData
     * @param $shelfBanner
     * @param $shelfName
     *
     * @return int
     *
     * @throws ShopsException
     */
    public function add($shelfData, $shelfBanner, $shelfName)
    {
        if (is_callable($shelfData)) {
            $shelf = call_user_func($shelfData, new ShelfData());
            if (!($shelf instanceof ShelfData)) {
                throw new ShopsException('必须返回 Shop\Data\Shelf class');
            }
            $shelfData = $shelf->toArray();
        }

        //todo 判断出ｂｕｇ
        //if (!is_array($shelfData)) throw new ShopsException('$shelfData　必须是数组');

        $this->response = $this->http->jsonPost(self::API_ADD, array(
            'shelf_data' => array(
                'module_infos' => $shelfData,
            ),
            'shelf_banner' => $shelfBanner,
            'shelf_name' => $shelfName,
        ));

        return $this->getResponse();
    }

    /**
     * 删除货架.
     *
     * @param int $shelfId
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function delete($shelfId)
    {
        $this->response = $this->http->jsonPost(self::API_DELETE, array('shelf_id' => $shelfId));

        return $this->getResponse();
    }

    /**
     * 修改货架.
     *
     * @param array|callable $shelfData
     * @param $shelfId
     * @param $shelfBanner
     * @param $shelfName
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function update($shelfData, $shelfId, $shelfBanner, $shelfName)
    {
        if (is_callable($shelfData)) {
            $shelf = call_user_func($shelfData, new ShelfData());
            if (!($shelf instanceof ShelfData)) {
                throw new ShopsException('必须返回 Shop\Data\Shelf class');
            }
            $shelfData = $shelf->toArray();
        }

        //todo 判断出ｂｕｇ
        //if (!is_array($shelfData)) throw new ShopsException('$shelfData　必须是数组');

        $this->response = $this->http->jsonPost(self::API_UPDATE, array(
            'shelf_id' => $shelfId,
            'shelf_data' => array(
                'module_infos' => $shelfData,
            ),
            'shelf_banner' => $shelfBanner,
            'shelf_name' => $shelfName,
        ));

        return $this->getResponse();
    }

    /**
     * 获取所有货架.
     *
     * @return array
     *
     * @throws ShopsException
     */
    public function lists()
    {
        $this->response = $this->http->get(self::API_LISTS);

        return $this->getResponse();
    }

    /**
     * 根据货架ID获取货架信息.
     *
     * @param $shelfId
     *
     * @return array
     *
     * @throws ShopsException
     */
    public function getById($shelfId)
    {
        $this->response = $this->http->jsonPost(self::API_GET_BY_ID, array('shelf_id' => $shelfId));

        return $this->getResponse();
    }
}
