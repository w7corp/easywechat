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
 * Product.php.
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

namespace Overtrue\Wechat\Shop\Data;

use Overtrue\Wechat\Shop\Foundation\ShopsException;
use Overtrue\Wechat\Shop\Stock;
use Overtrue\Wechat\Utils\MagicAttributes;

/**
 * 商品属性.
 *
 * Class ProductData
 *
 * @property array $express
 * @property array $attrext
 */
class Product extends MagicAttributes
{
    private $shelf;

    /**
     * 是否在货架上.
     *
     * @param bool|false $shelf
     */
    public function __construct($shelf = false)
    {
        $this->shelf = $shelf;
    }

    /**
     * 设置基本属性.
     *
     * @param $main_img
     * @param array $img
     * @param null  $buyLimit
     * @param null  $name
     * @param null  $categoryId
     *
     * @return $this
     *
     * @throws ShopsException
     */
    public function setBaseAttr($main_img, array $img, $buyLimit = null, $name = null, $categoryId = null)
    {
        $this->attributes['product_base'] = array(
            'main_img' => $main_img,
            'img' => $img,
        );

        if (!$this->shelf) {
            $this->attributes['product_base']['name'] = $name;
            $this->attributes['product_base']['category_id'][] = $categoryId;
        } else {
            if (!empty($name)) {
                throw new ShopsException('请下架之后在设置name');
            }
            if (!empty($category)) {
                throw new ShopsException('请下架之后在设置 category');
            }
        }

        if (!empty($buyLimit)) {
            $this->attributes['product_base']['buy_limit'] = $buyLimit;
        }

        return $this;
    }

    /**
     * 商品详情列表.
     *
     * @param $name
     * @param $value
     *
     * @return $this
     *
     * @throws ShopsException
     */
    public function setDetail($name, $value)
    {
        if ($name != 'text' && $name != 'img') {
            throw new ShopsException('name 只能为 text 或者 img ');
        }

        $this->attributes['product_base']['detail'][][$name] = $value;

        return $this;
    }

    /**
     * 商品属性列表.
     *
     * @param $id
     * @param $vid
     *
     * @return $this
     */
    public function setProperty($id, $vid)
    {
        $this->attributes['product_base']['property'][] = array(
            'id' => $id,
            'vid' => $vid,
        );

        return $this;
    }

    /**
     * 设置sku.
     *
     * @param $id
     * @param array $vid
     *
     * @return $this
     */
    public function setSkuInfo($id, array $vid)
    {
        // '$属性' '$值'
        // 123123  '$值'
        $this->attributes['product_base']['sku_info'][] = array(
        'id' => $id,
        'vid' => $vid,
    );

        return $this;
    }

    /**
     * sku信息列表.
     *
     * @param $oriPrice
     * @param $price
     * @param $iconUrl
     * @param $quantity
     * @param null $skuId
     *
     * @return $this
     */
    public function setSkuList($oriPrice, $price, $iconUrl, $quantity, $skuId = null)
    {
        $data = array(
            'ori_price' => $oriPrice,
            'price' => $price,
            'icon_url' => $iconUrl,
            'quantity' => $quantity,
        );

        if (is_array($skuId)) {
            $skuId = Stock::getSkuInfo($skuId);
        }

        if (!empty($skuId)) {
            $data['sku_id'] = $skuId;
        }

        $this->attributes['sku_list'][] = $data;

        return $this;
    }

    /**
     * 商品其他属性.
     *
     * @param $isPostFree
     * @param $isHasReceipt
     * @param $isUnderGuaranty
     * @param $isSupportReplace
     *
     * @return $this
     */
    public function setAttrext($isPostFree, $isHasReceipt, $isUnderGuaranty, $isSupportReplace)
    {
        $this->attrext = array(
            'isPostFree' => $isPostFree,
            'isHasReceipt' => $isHasReceipt,
            'isUnderGuaranty' => $isUnderGuaranty,
            'isSupportReplace' => $isSupportReplace,
        );

        return $this;
    }

    public function setLocation($province, $city, $address, $country = '中国')
    {
        $this->attributes['attrext']['location'] = array(
            'country' => $country,
            'province' => $province,
            'city' => $city,
            'address' => $address,
        );

        return $this;
    }

    /**
     * 运费信息.
     *
     * @param $deliveryType
     * @param null $template
     *
     * @return $this
     *
     * @throws ShopsException
     */
    public function setDeliveryInfo($deliveryType, $template = null)
    {
        $data['delivery_type'] = $deliveryType;

        if (!empty($template)) {
            if (!is_string($template)) {
                throw new ShopsException('错误数据类型');
            }
            $data['template_id'] = $template;
        }

        $this->attributes['delivery_info'] = $data;

        return $this;
    }

    /**
     * 默认模板
     *
     * @param $id
     * @param $price
     *
     * @return $this
     */
    public function setExpress($id, $price)
    {
        $this->attributes['delivery_info']['express'][] = array(
            'id' => $id,
            'price' => $price,
        );

        return $this;
    }

    /**
     * 拼接最后的data.
     *
     * @return array
     *
     * @throws ShopsException
     */
    public function toArray()
    {
        if ($this->shelf) {
            if (isset($this->attributes['product_base']['Property'])) {
                throw new ShopsException('不能设置　Property　字段,请下架商品再修改');
            }
        }

        if (empty($this->attributes['product_base'])) {
            throw new ShopsException('baseAttr 不允许为空');
        }
        if (empty($this->attributes['sku_list'])) {
            throw new ShopsException('skuList 不允许为空');
        }
        if (empty($this->attrext)) {
            throw new ShopsException('attrext 不允许为空');
        }

        if ($this->attributes['attrext']['isPostFree'] == 0) {
            if (empty($this->attributes['delivery_info'])) {
                throw new ShopsException('deliveryInfo 不允许为空');
            }

            if ($this->attributes['delivery_info']['delivery_type'] == 0) {
                if (empty($this->attributes['delivery_info']['express'])) {
                    throw new ShopsException('express 不允许为空');
                }
                $this->attributes['delivery_info']['express'] = $this->express;
            }
        }

        return $this->attributes;
    }

    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }
}
