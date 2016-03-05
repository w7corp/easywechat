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
 * ShopException.php.
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

namespace Overtrue\Wechat\Shop\Foundation;

use Exception;

/**
 * 异常类.
 *
 * Class ShopsException
 */
class ShopsException extends Exception
{
    public function __construct($message, $code = null)
    {
        if (!empty($code)) {
            $message = '[Wechat]错误信息：'.$message.'错误代码：'.$code;
        }

        parent::__construct($message);
    }
}
