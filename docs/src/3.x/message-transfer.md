# 多客服消息转发



多客服的消息转发绝对是超级的简单，转发的消息类型为 `transfer`：

```php


  // 转发收到的消息给客服
  $server->setMessageHandler(function($message) {
      return new \EasyWeChat\Message\Transfer();
  });

  $result = $server->serve();

  echo $result;
```

当然，你也可以指定转发给某一个客服：

```php
$server->setMessageHandler(function($message) {
    $transfer = new \EasyWeChat\Message\Transfer();
    $transfer->account($account);// 或者 $transfer->to($account);

    return $transfer;
});
```

更多请参考 [微信官方文档](http://mp.weixin.qq.com/wiki/) **多客服消息转发** 章节