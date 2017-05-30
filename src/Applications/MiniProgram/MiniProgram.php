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

namespace EasyWeChat\Applications\MiniProgram;

use EasyWeChat\Support\Traits\PrefixedContainer;

/**
 * Class MiniProgram.
 *
 * @property \EasyWeChat\Applications\MiniProgram\Sns\Sns $sns
 * @property \EasyWeChat\Applications\MiniProgram\Stats\Stats $stats
 * @property \EasyWeChat\Applications\MiniProgram\Server\Guard $server
 * @property \EasyWeChat\Applications\MiniProgram\TemplateMessage\TemplateMessage $template_message
 * @property \EasyWeChat\Applications\MiniProgram\QRCode\QRCode $qrcode
 * @property \EasyWeChat\Applications\MiniProgram\Material\Temporary $material_temporary
 * @property \EasyWeChat\Applications\MiniProgram\CustomerService\CustomerService $customer_service
 */
class MiniProgram
{
    use PrefixedContainer;
}
