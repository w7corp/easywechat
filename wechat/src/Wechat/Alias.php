<?php
/**
 * Alias.php
 *
 * Part of MasApi\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace MasApi\Wechat;

/**
 * SDK 服务别名
 */
class Alias
{

    /**
     * 别名对应关系
     *
     * @var array
     */
    protected static $aliases = array(
                                 'WechatAuth'            => 'MasApi\\Wechat\\Auth',
                                 'WechatCard'            => 'MasApi\\Wechat\\Card',
                                 'WechatException'       => 'MasApi\\Wechat\\Exception',
                                 'WechatGroup'           => 'MasApi\\Wechat\\Group',
                                 'WechatImage'           => 'MasApi\\Wechat\\Image',
                                 'WechatJs'              => 'MasApi\\Wechat\\Js',
                                 'WechatMedia'           => 'MasApi\\Wechat\\Media',
                                 'WechatMenu'            => 'MasApi\\Wechat\\Menu',
                                 'WechatMenuItem'        => 'MasApi\\Wechat\\MenuItem',
                                 'WechatMessage'         => 'MasApi\\Wechat\\Message',
                                 'WechatBaseMessage'     => 'MasApi\\Wechat\\Messages\\BaseMessage',
                                 'WechatImageMessage'    => 'MasApi\\Wechat\\Messages\\Image',
                                 'WechatLinkMessage'     => 'MasApi\\Wechat\\Messages\\Link',
                                 'WechatLocationMessage' => 'MasApi\\Wechat\\Messages\\Location',
                                 'WechatMusicMessage'    => 'MasApi\\Wechat\\Messages\\Music',
                                 'WechatNewsMessage'     => 'MasApi\\Wechat\\Messages\\News',
                                 'WechatNewsMessageItem' => 'MasApi\\Wechat\\Messages\\NewsItem',
                                 'WechatTextMessage'     => 'MasApi\\Wechat\\Messages\\Text',
                                 'WechatTransferMessage' => 'MasApi\\Wechat\\Messages\\Transfer',
                                 'WechatVideoMessage'    => 'MasApi\\Wechat\\Messages\\Video',
                                 'WechatVoiceMessage'    => 'MasApi\\Wechat\\Messages\\Voice',
                                 'WechatQRCode'          => 'MasApi\\Wechat\\QRCode',
                                 'WechatServer'          => 'MasApi\\Wechat\\Server',
                                 'WechatStaff'           => 'MasApi\\Wechat\\Staff',
                                 'WechatStore'           => 'MasApi\\Wechat\\Store',
                                 'WechatUrl'             => 'MasApi\\Wechat\\Url',
                                 'WechatUser'            => 'MasApi\\Wechat\\User',
                                 'WechatNotice'          => 'MasApi\\Wechat\\Notice',
                                 'WechatStats'           => 'MasApi\\Wechat\\Stats',
                                 'WechatSemantic'        => 'MasApi\\Wechat\\Semantic',
                                 'WechatColor'           => 'MasApi\\Wechat\\Color',
                                );

    /**
     * 是否已经注册过
     *
     * @var bool
     */
    protected static $registered = false;

    /**
     * 注册别名
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
