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
 * Alias.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

/**
 * SDK 服务别名.
 */
class Alias
{
    /**
     * 别名对应关系.
     *
     * @var array
     */
    protected static $aliases = array(
                                 'WechatAuth' => 'Overtrue\\Wechat\\Auth',
                                 'WechatCard' => 'Overtrue\\Wechat\\Card',
                                 'WechatException' => 'Overtrue\\Wechat\\Exception',
                                 'WechatGroup' => 'Overtrue\\Wechat\\Group',
                                 'WechatImage' => 'Overtrue\\Wechat\\Image',
                                 'WechatJs' => 'Overtrue\\Wechat\\Js',
                                 'WechatMedia' => 'Overtrue\\Wechat\\Media',
                                 'WechatMenu' => 'Overtrue\\Wechat\\Menu',
                                 'WechatMenuItem' => 'Overtrue\\Wechat\\MenuItem',
                                 'WechatMessage' => 'Overtrue\\Wechat\\Message',
                                 'WechatBaseMessage' => 'Overtrue\\Wechat\\Messages\\BaseMessage',
                                 'WechatImageMessage' => 'Overtrue\\Wechat\\Messages\\Image',
                                 'WechatLinkMessage' => 'Overtrue\\Wechat\\Messages\\Link',
                                 'WechatLocationMessage' => 'Overtrue\\Wechat\\Messages\\Location',
                                 'WechatMusicMessage' => 'Overtrue\\Wechat\\Messages\\Music',
                                 'WechatNewsMessage' => 'Overtrue\\Wechat\\Messages\\News',
                                 'WechatNewsMessageItem' => 'Overtrue\\Wechat\\Messages\\NewsItem',
                                 'WechatTextMessage' => 'Overtrue\\Wechat\\Messages\\Text',
                                 'WechatTransferMessage' => 'Overtrue\\Wechat\\Messages\\Transfer',
                                 'WechatVideoMessage' => 'Overtrue\\Wechat\\Messages\\Video',
                                 'WechatVoiceMessage' => 'Overtrue\\Wechat\\Messages\\Voice',
                                 'WechatQRCode' => 'Overtrue\\Wechat\\QRCode',
                                 'WechatServer' => 'Overtrue\\Wechat\\Server',
                                 'WechatStaff' => 'Overtrue\\Wechat\\Staff',
                                 'WechatStore' => 'Overtrue\\Wechat\\Store',
                                 'WechatUrl' => 'Overtrue\\Wechat\\Url',
                                 'WechatUser' => 'Overtrue\\Wechat\\User',
                                 'WechatNotice' => 'Overtrue\\Wechat\\Notice',
                                 'WechatStats' => 'Overtrue\\Wechat\\Stats',
                                 'WechatSemantic' => 'Overtrue\\Wechat\\Semantic',
                                 'WechatColor' => 'Overtrue\\Wechat\\Color',
                                );

    /**
     * 是否已经注册过.
     *
     * @var bool
     */
    protected static $registered = false;

    /**
     * 注册别名.
     */
    public static function register()
    {
        if (!self::$registered) {
            foreach (self::$aliases as $alias => $class) {
                class_alias($class, $alias);
            }

            self::$registered = true;
        }
    }
}
