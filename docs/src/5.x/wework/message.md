# 消息

## 主动发送消息

```php
use EasyWeChat\Kernel\Messages\TextCard;


// 获取 Messenger 实例
$messenger = $app->messenger;

// 准备消息
$message = new TextCard([
    'title' => '你的请假单审批通过', 
    'description' => '单号：1928373, ....', 
    'url' => 'http://easywechat.com/oa/....'
]);

// 发送
$messenger->message($message)->toUser('overtrue')->send();

```

你也可以很方便的发送普通文本消息：

```php
$messenger->message('你的请假单（单号：1928373）已经审批通过！')->toUser('overtrue')->send();
// 或者写成
$messenger->toUser('overtrue')->send('你的请假单（单号：1928373）已经审批通过！');
```

## 接收消息

被动接收消息，与回复消息，请参考：[服务端](server)


## 更新任务卡片消息状态 

```php
$messenger->updateTaskcard(array $userids, int $agentId, string $taskId, string $replaceName = '已收到')
```

