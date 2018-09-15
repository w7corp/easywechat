## 我用的环境

PHP 版本：
~~~
PHP7.0+
~~~
overtrue/wechat 版本：
~~~
全系列
~~~
是否使用了框架？框架名称：
~~~
Yii2.0(但是问题应该与框架无关)
~~~

## 问题及现象

描述你的问题现象，报错**贴截图**粘贴或者贴具体信息，提供**必要的代码段**
~~~
公众号平台发送文件类型的信息时,无法处理信息
经过检查

master 版本下,文件:

src/Kernel/Messages/Message.php
 
 第40行:
 const ALL = 1049598;
 代码错误
 
 正确应该是: const ALL = 1050622
 
 (代表文件类型的 const FILE = 1024;没有计算到ALL里去)
 
 ====
 
 3.X版本下,有同样问题:
 
 文件:
 
 src/Server/Guard.php

 第58行
~~~

如果你不提供相关的代码，我不会做任何应答，直接 close，感谢！


<!-- Love wechat? Please consider supporting our collective:
👉  https://opencollective.com/wechat/donate -->