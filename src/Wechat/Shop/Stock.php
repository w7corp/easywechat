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
 * Stock.php.
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
use Overtrue\Wechat\Shop\Foundation\ShopsException;
use Overtrue\Wechat\Shop\Foundation\Stock as StockInterface;

/**
 * Class Stock.
 */
class Stock extends Base implements StockInterface
{
    const API_ADD = 'https://api.weixin.qq.com/merchant/stock/add';
    const API_REDUCE = 'https://api.weixin.qq.com/merchant/stock/reduce';

    /**
     * 增加库存.
     *
     * @param $productId
     * @param $quantity
     * @param string|array $skuInfo
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function add($productId, $quantity, $skuInfo = null)
    {
        $this->response = $this->http->jsonPost(self::API_ADD, array(
            'product_id' => $productId,
            'sku_info' => is_null($skuInfo) ? '' : $this->getSkuInfo($skuInfo),
            'quantity' => $quantity,
        ));

        return $this->getResponse();
    }

    /**
     * 减少库存.
     *
     * @param array        $productId
     * @param string|array $skuInfo
     * @param int          $quantity
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function reduce($productId, $quantity, $skuInfo = null)
    {
        $this->response = $this->http->jsonPost(self::API_REDUCE, array(
            'product_id' => $productId,
            'sku_info' => is_null($skuInfo) ? '' : $this->getSkuInfo($skuInfo),
            'quantity' => $quantity,
        ));

        return $this->getResponse();
    }

    /**
     * 拼装 SkuInfo Str.
     *
     * @param array $skuInfo
     *
     * @return string
     */
    public static function getSkuInfo($skuInfo)
    {
        if (is_string($skuInfo)) {
            return $skuInfo;
        }

        $str = '';
//        array(
//            array('id','vid'),
//            array('id1','vid1'),
//            //.....
//        );
        $i = 0;
        foreach ($skuInfo as $v) {
            ++$i;
            if (count($skuInfo) > $i) {
                $str .= $v[0].':'.$v[1].';';
            } else {
                $str .= $v[0].':'.$v[1];
            }
        }

        return $str;
    }
}
