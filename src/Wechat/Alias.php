<?php
namespace Overtrue\Wechat;

class Alias
{
    protected static $aliases = [
               'WechatAuth'            => 'Overtrue\\Wechat\\Auth',
               'WechatCard'            => 'Overtrue\\Wechat\\Card',
               'WechatException'       => 'Overtrue\\Wechat\\Exception',
               'WechatGroup'           => 'Overtrue\\Wechat\\Group',
               'WechatImage'           => 'Overtrue\\Wechat\\Image',
               'WechatJs'              => 'Overtrue\\Wechat\\Js',
               'WechatMedia'           => 'Overtrue\\Wechat\\Media',
               'WechatMenu'            => 'Overtrue\\Wechat\\Menu',
               'WechatMenuItem'        => 'Overtrue\\Wechat\\MenuItem',
               'WechatMessage'         => 'Overtrue\\Wechat\\Message',
               'WechatBaseMessage'     => 'Overtrue\\Wechat\\Messages\\BaseMessage',
               'WechatMessageImage'    => 'Overtrue\\Wechat\\Messages\\Image',
               'WechatMessageLink'     => 'Overtrue\\Wechat\\Messages\\Link',
               'WechatMessageLocation' => 'Overtrue\\Wechat\\Messages\\Location',
               'WechatMessageMusic'    => 'Overtrue\\Wechat\\Messages\\Music',
               'WechatMessageNews'     => 'Overtrue\\Wechat\\Messages\\News',
               'WechatMessageNewsItem' => 'Overtrue\\Wechat\\Messages\\NewsItem',
               'WechatMessageText'     => 'Overtrue\\Wechat\\Messages\\Text',
               'WechatMessageTransfer' => 'Overtrue\\Wechat\\Messages\\Transfer',
               'WechatMessageVideo'    => 'Overtrue\\Wechat\\Messages\\Video',
               'WechatMessageVoice'    => 'Overtrue\\Wechat\\Messages\\Voice',
               'WechatQRCode'          => 'Overtrue\\Wechat\\QRCode',
               'WechatServer'          => 'Overtrue\\Wechat\\Server',
               'WechatStaff'           => 'Overtrue\\Wechat\\Staff',
               'WechatStore'           => 'Overtrue\\Wechat\\Store',
               'WechatUrl'             => 'Overtrue\\Wechat\\Url',
               'WechatUser'            => 'Overtrue\\Wechat\\User',
    ];

    /**
     * 是否已经注册过
     *
     * @var boolean
     */
    protected static $registered = false;

    /**
     * 注册别名
     *
     * @return void
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