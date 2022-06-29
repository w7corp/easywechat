> 👋🏼 您当前浏览的文档为 6.x，其它版本的文档请参考：[5.x](/5.x/)、[4.x](/4.x/)、[3.x](/3.x/)

# EasyWeChat

EasyWeChat 是一个开源的 [微信](http://www.wechat.com) 非官方 SDK。安装非常简单，因为它是一个标准的 [Composer](https://getcomposer.org/) 包，这意味着任何满足下列安装条件的 PHP 项目支持 Composer 都可以使用它。

## 环境需求

- PHP >= 8.0
- [PHP cURL 扩展](http://php.net/manual/en/book.curl.php)
- [PHP OpenSSL 扩展](http://php.net/manual/en/book.openssl.php)
- [PHP SimpleXML 扩展](http://php.net/manual/en/book.simplexml.php)
- [PHP fileinfo 拓展](http://php.net/manual/en/book.fileinfo.php)

## 安装

```shell
composer require w7corp/easywechat:^6.7
```

## 使用

从 6.x 起，EasyWeChat 依然保持了它开箱即用的特性，同样只需要传入一个配置，初始化一个模块实例即可：

```php
use EasyWeChat\OfficialAccount\Application;

$config = [
    'app_id' => 'wx3cf0f39249eb0exx',
    'secret' => 'f1c242f4f28f735d4687abb469072axx',
    'token' => 'easywechat',
    'aes_key' => '' // 明文模式请勿填写 EncodingAESKey
    //...
];

$app = new Application($config);
```

在创建实例后，所有的方法都几乎可以有 IDE 自动补全，当然，建议先阅读各模块的文档了解一下它们的区别，这里我们以调用公众号获取用户资料为例：

```php
$response = $app->getClient()->get("/cgi-bin/user/info?openid={$openid}&lang=zh_CN");

# 查看返回结果
var_dump($response->toArray());
```

## 开始之前

在你动手写代码之前，建议您首先阅读以下内容：

- [关于 6.x](./introduction.md)
- [API 调用](./client.md)

## 参与贡献

我们欢迎广大开发者贡献大家的智慧，让我们共同让它变得更完美。您可以在 GitHub 上提交 Pull Request，我们会尽快审核并公布。更多信息请参考 [贡献指南](contributing.md)。

## 开发者交流群

[EasyWeChat SDK 交流群](http://shang.qq.com/wpa/qunwpa?idkey=b4dcf3ec51a7e8c3c3a746cf450ce59895e5c4ec4fbcb0f80c2cd97c3c6e63e9) ID: 319502940
