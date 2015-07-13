rm -rf ./subsplit
git subsplit init git@github.com:overtrue/wechat.git
git subsplit update
git subsplit publish "
    src/EasyWeChat/Core:git@github.com:easywechat/core.git
    src/EasyWeChat/Cache:git@github.com:easywechat/cache.git
    src/EasyWeChat/Support/:git@github.com:easywechat/support.git
    src/EasyWeChat/User/:git@github.com:easywechat/user.git
    src/EasyWeChat/Menu/:git@github.com:easywechat/menu.git
    src/EasyWeChat/Js/:git@github.com:easywechat/js.git
    src/EasyWeChat/Semantic/:git@github.com:easywechat/semantic.git
    src/EasyWeChat/Store/:git@github.com:easywechat/store.git
    src/EasyWeChat/Tool/:git@github.com:easywechat/tool.git
    src/EasyWeChat/Material/:git@github.com:easywechat/material.git
    src/EasyWeChat/Notice/:git@github.com:easywechat/notice.git
    src/EasyWeChat/Server/:git@github.com:easywechat/server.git
    src/EasyWeChat/Stats/:git@github.com:easywechat/stats.git
    src/EasyWeChat/Staff/:git@github.com:easywechat/staff.git
    src/EasyWeChat/QRCode/:git@github.com:easywechat/qrcode.git
    src/EasyWeChat/Url/:git@github.com:easywechat/url.git
    src/EasyWeChat/OAuth/:git@github.com:easywechat/oauth.git
    src/EasyWeChat/Payment/:git@github.com:easywechat/payment.git
    src/EasyWeChat/Device/:git@github.com:easywechat/device.git
    src/EasyWeChat/Encryption/:git@github.com:easywechat/encryption.git
    src/EasyWeChat/Event/:git@github.com:easywechat/event.git
    src/EasyWeChat/Message/:git@github.com:easywechat/message.git
" --no-tags --heads=develop