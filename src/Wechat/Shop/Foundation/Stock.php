<?php
/**
 * Stock.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a939638621 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Shop\Foundation;

/**
 * 库存
 *
 * Interface Stock
 * @package Shop
 */
interface Stock
{
    /**
     * 增加库存
     *
     * @param $productId
     * @param array $skuInfo
     * @param $quantity
     * @return bool
     */
    public function add($productId, $skuInfo, $quantity);

    /**
     * 减少库存
     *
     * @param $productId
     * @param array $skuInfo
     * @param $quantity
     * @return mixed
     */
    public function reduce($productId, $skuInfo, $quantity);
}