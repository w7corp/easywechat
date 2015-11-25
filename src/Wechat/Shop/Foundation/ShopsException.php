<?php
/**
 * ShopException.php
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

use Exception;

/**
 * 异常类
 *
 * Class ShopsException
 * @package Shop
 */
class ShopsException extends Exception
{
    function __construct($message,$code = null)
    {
        if (!empty($code)) $message = '[Wechat]错误信息：'.$message.'错误代码：'.$code;

        parent::__construct($message);
    }

}