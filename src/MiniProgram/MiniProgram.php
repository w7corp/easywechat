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
 * MiniProgram.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\MiniProgram;

use EasyWeChat\Support\Traits\PrefixedContainer;

/**
 * Class MiniProgram.
 *
 * @property \EasyWeChat\MiniProgram\Server\Guard $server
 * @property \EasyWeChat\MiniProgram\Sns\Sns $sns
 * @property \EasyWeChat\MiniProgram\Notice\Notice $notice
 * @property \EasyWeChat\MiniProgram\Staff\Staff $staff
 * @property \EasyWeChat\MiniProgram\QRCode\QRCode $qrcode
 * @property \EasyWeChat\MiniProgram\Material\Temporary $material_temporary
 * @property \EasyWeChat\MiniProgram\Stats\Stats $stats
 * @property \EasyWeChat\MiniProgram\Transaction\Transaction $transaction
 * @property \EasyWeChat\MiniProgram\Transaction\Order\Order $order
 * @property \EasyWeChat\MiniProgram\Transaction\Spu\Product $product
 * @property \EasyWeChat\MiniProgram\Transaction\Delivery\Delivery $delivery
 * @property \EasyWeChat\MiniProgram\Transaction\AfterSale\AfterSale $aftersale
 */
class MiniProgram
{
    use PrefixedContainer;
}
