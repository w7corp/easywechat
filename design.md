主类：\Wechat
    SEC_MODE_PLAIN_TEXT; //明文模式
    SEC_MODE_COMPATIBLE; //明文模式
    SEC_MODE_SECURITY;   //安全模式

    _construct($appId, $appSecret);
    setSecurityMode($mode); //明文模式、兼容模式、安全模式
    listen($event, callback $function); // 监听事件
    error(callback $callback); //错误时处理函数

消息：\Wechat\Message
        \Wechat\Message\Text      文字消息
        \Wechat\Message\Image     图片消息
        \Wechat\Message\Voice     语音消息
        \Wechat\Message\Video     视频消息
        \Wechat\Message\Location  地理位置消息
        \Wechat\Message\Link      链接消息
        \Wechat\Message\Music     音乐消息
        \Wechat\Message\Articles  图文消息

消息模板：\Wechat\Template

事件：\Wechat\Event
        \Wechat\Event\Subscribe 关注与取消关注
        \Wechat\Event\QRCode    扫描带参数二维码事件
        \Wechat\Event\Location  上报地理位置事件
        \Wechat\Event\Menu      自定义菜单事件
        \Wechat\Event\Message   消息事件

用户：\Wechat\User
        \Wechat\User\Group 用户组

菜单：\Wechat\Menu

工具：\Wechat\Util
    
    \Wechat\Util\QRCode  二维码
        make($content, $expireSeconds = 1800);
        temporary(); 临时的,连贯操作:make()->temporary();
        permanent(); 永久的

    \Wechat\Util\Crypt          加密解密器
        encrypt();
        decrypt();

    \Wechat\Util\Crypt\PKCS7
    \Wechat\Util\Crypt\SHA1
    \Wechat\Util\Crypt\Error

错误：\Wechat\Error
    _construct($code)
    _toString();

媒体：\Wechat\Media
    image();
    voice();
    video();
    thumb();
    upload();
    get($id);
