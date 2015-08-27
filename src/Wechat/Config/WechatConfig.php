<?php
/**
 * WechatConfig.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    peiwen <haopeiwen123@gmail.com>
 * @copyright 2015 peiwen <haopeiwen123@gmail.com>
 * @link      https://github.com/troubleman
 * @link      https://github.com/troubleman/Wechat
 */

namespace Overtrue\Wechat\Config;

class WechatConfig
{
    //=======【基本信息设置】=====================================
    //微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
    const APPID     = 'xxxxxxxxxxxxxxxxxxxx';
    //JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
    const APPSECRET = 'xxxxxxxxxxxxxxxxxxxx';
    //受理商ID，身份标识
    const MCHID     = 'xxxxxxxxxxxxxxxxxxxx';
    //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
    const KEY       = 'xxxxxxxxxxxxxxxxxxxx';
    
    //=======【证书路径设置】=====================================
    //证书路径,注意应该填写绝对路径
    //证书文件不能放在web服务器虚拟目录，应放在有访问权限控制的目录中，防止被他人下载。
    const SSLCERT_PATH = 'xxxxxxxxxxxxxxxxxxxx';
    const SSLKEY_PATH  = 'xxxxxxxxxxxxxxxxxxxx';
}
    
?>