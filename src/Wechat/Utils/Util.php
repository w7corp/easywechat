<?php
/**
 * Util.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Frye <frye0423@gmail.com>
 * @copyright 2015 Frye <frye0423@gmail.com>
 * @link      https://github.com/0i
 * @link      http://blog.lost-magic.com
 * @link      https://github.com/thenbsp/Wechat
 */

namespace Overtrue\Wechat\Utils;

class Util
{
    /**
     * 获取随机字符串
     * @param int $length
     *
     * @return string
     */
    public static function randomString($length = 16)
    {
        $characters         = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength   = strlen($characters);
        $randomString       = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * 获取客户端 IP, 强烈简直传入参数的时候就传入配置, 不要让系统自动获取,
     * 因为HTTP所有Header都是可以伪造的, 这个方法获取的可能是伪造的IP
     * @return string
     */
    public static function clientIP()
    {
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = '0.0.0.0';
        return $ipaddress;
    }

}