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
 * Group.php.
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

use Overtrue\Wechat\Shop\Foundation\Base;
use Overtrue\Wechat\Shop\Foundation\Group as GroupInterface;
use Overtrue\Wechat\Shop\Foundation\ShopsException;

/**
 * 分组管理.
 *
 * Class Group
 */
class Group extends Base implements GroupInterface
{
    const API_ADD = 'https://api.weixin.qq.com/merchant/group/add';
    const API_DELETE = 'https://api.weixin.qq.com/merchant/group/del';
    const API_UPDATE_ATTRIBUTE = 'https://api.weixin.qq.com/merchant/group/propertymod';
    const API_UPDATE_PRODUCT = 'https://api.weixin.qq.com/merchant/group/productmod';
    const API_LISTS = 'https://api.weixin.qq.com/merchant/group/getall';
    const API_GET_BY_ID = 'https://api.weixin.qq.com/merchant/group/getbyid';

    /**
     * 添加分组.
     *
     * @param $groupName
     * @param array $productList
     *
     * @return mixed
     *
     * @throws
     */
    public function add($groupName, array $productList)
    {
        foreach (array_keys($productList) as $v) {
            if (!is_numeric($v)) {
                throw new ShopsException('请插入索引数组');
            }
        }

        $this->response = $this->http->jsonPost(self::API_ADD, array(
            'group_detail' => array(
                'group_name' => $groupName,
                'product_list' => $productList,
            ),
        ));

        return $this->getResponse();
    }

    /**
     * 删除分组.
     *
     * @param $groupId
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function delete($groupId)
    {
        $this->response = $this->http->jsonPost(self::API_DELETE, array(
            'group_id' => $groupId,
        ));

        return $this->getResponse();
    }

    /**
     * 修改分组属性.
     *
     * @param $groupId
     * @param $groupName
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function updateAttribute($groupId, $groupName)
    {
        $this->response = $this->http->jsonPost(self::API_UPDATE_ATTRIBUTE, array(
            'group_id' => $groupId,
            'group_name' => $groupName,
        ));

        return $this->getResponse();
    }

    /**
     * 修改分组商品
     *
     * @param $groupId
     * @param array $product
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function updateProduct($groupId, array $product)
    {
        foreach ($product as $v) {
            $keys = array_keys($v);

            if (count($keys) == 2) {
                if (!($keys[0] == 'product_id' && $keys[1] == 'mod_action')) {
                    $data[] = array('product_id' => $v[$keys[0]], 'mod_action' => $v[$keys[1]]);
                }
            }
        }

        $this->response = $this->http->jsonPost(self::API_UPDATE_PRODUCT, array(
            'group_id' => $groupId,
            'product' => isset($data) && is_array($data) ? $data : $product,
        ));

        return $this->getResponse();
    }

    /**
     * 获得全部商品
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
     * 根据分组ID获取分组信息.
     *
     * @param $groupId
     *
     * @return array
     *
     * @throws ShopsException
     */
    public function getById($groupId)
    {
        $this->response = $this->http->jsonPost(self::API_GET_BY_ID, array(
            'group_id' => $groupId,
        ));

        return $this->getResponse();
    }
}
