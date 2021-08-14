# EasyWeChat

EasyWeChat 是一个开源的 [微信](http://www.wechat.com) 非官方 SDK。

EasyWeChat 的安装非常简单，因为它是一个标准的 [Composer](https://getcomposer.org/) 包，这意味着任何满足下列安装条件的 PHP 项目支持 Composer 都可以使用它。

## 环境需求

- PHP >= 8.0
- [PHP cURL 扩展](http://php.net/manual/en/book.curl.php)
- [PHP OpenSSL 扩展](http://php.net/manual/en/book.openssl.php)
- [PHP SimpleXML 扩展](http://php.net/manual/en/book.simplexml.php)
- [PHP fileinfo 拓展](http://php.net/manual/en/book.fileinfo.php)

## 加入我们

[EasyWeChat SDK 交流群](http://shang.qq.com/wpa/qunwpa?idkey=b4dcf3ec51a7e8c3c3a746cf450ce59895e5c4ec4fbcb0f80c2cd97c3c6e63e9) ID: 319502940

> {warning} 为了避免广告及不看文档用户，加群需要付费，所以请使用 能支持群费的客户端。
> 另外：付费加群不代表我们有责任在群里回答你的问题，所以请认真阅读微信官方文档与 SDK 使用文档再使用，否则提的低级问题不会有人理你
> 不喜勿加，谢谢！
> 除非你发现了明确的 Bug，否则不要在群里 @ 我 :pray:

你有以下两种方式加入到我们中来，为广大开发者提供更优质的免费开源的服务：

- **贡献代码**：我们的代码都在 [w7corp/easywechat](https://github.com/w7corp/easywechat) ，你可以提交 PR 到任何一个项目，当然，前提是代码质量必须是 OK 的。
- **翻译或补充文档**：我们的文档在：[EasyWeChat/docs](https://github.com/easywechat/docs/)，你可以提交对应的 PR 到目标分支参与翻译工作。

### 开始之前

本 SDK 不是一个全新再造的东西，你完全有必要在使用本 SDK 前做好以下工作：

- 熟悉 PHP 常见的知识：自动加载、Composer 的使用、JSON 处理、cURL 的使用等；
- **仔细阅读并看懂** [微信官方文档](https://developers.weixin.qq.com/doc/)；
- 明白微信接口的组成，自有服务器、微信服务器、公众号（还有其它各种号）、测试号、以及通信原理（交互过程）；
- 了解基本的 HTTP 协议，Header 头、请求方式（GET\POST\PUT\PATCH\DELETE）等；
- 基本的 debug 技能，查看 PHP 日志，Nginx 日志等。

如果你不具备这些知识，请不要使用，因为用起来会比较痛苦。

另外请正确提问：

- [断言：不懂《提问的智慧》的人不会从初级程序员水平毕业](https://learnku.com/laravel/t/535/assertion-people-who-do-not-understand-the-wisdom-of-asking-questions-will-not-graduate-from-junior-programmers)
- [PHP 之道](http://laravel-china.github.io/php-the-right-way/)


我们专门针对一些容易出现的通用问题已经做了汇总： [疑难解答](/docs/master/troubleshooting) ，如果你在问题疑难解答没找到你出现的问题，那么可以在这里提问 [GitHub](https://github.com/overtrue/wechat/issues)，提问请描述清楚你用的版本，你的做法是什么，不然别人没法帮你。

> {warning} 最后，请有问题先审查代码，看文档, 再 Google，然后去群里提问题，带上你的代码，重现流程，大家有空的会帮忙你解答。谢谢合作！:pray:
