# 服务端

## 企业微信第三方回调协议

SDK 默认会处理事件 `suite_ticket` ，并会缓存 `suite_ticket`

> 需要注意的是：授权成功、变更授权、取消授权通知时间的响应必须在 1000ms 内完成，以保证用户安装应用的体验。建议在接收到此事件时 立即回应企业微信，之后再做相关业务的处理。

```php
$server = $app->server;

$server->push(function ($message) {
    //指令回调
    if (isset($message['InfoType'])) {
        switch ($message['InfoType']) {
            //推送suite_ticket
            case 'suite_ticket':
                break;
            //授权成功通知
            case 'create_auth':
                break;
            //变更授权通知
            case 'cancel_auth':
                break;
            //通讯录事件通知
            case 'change_contact':
                switch ($message['ChangeType']) {
                    case 'create_user':
                        return '新增成员事件';
                        break;
                    case 'update_user':
                        return '更新成员事件';
                        break;
                    case 'delete_user':
                        return '删除成员事件';
                        break;
                    case 'create_party':
                        return '新增部门事件';
                        break;
                    case 'update_party':
                        return '更新部门事件';
                        break;
                    case 'delete_party':
                        return '删除部门事件';
                        break;
                    case 'update_tag':
                        return '标签成员变更事件';
                        break;
                }
                break;
            default:
                return 'fail';
                break;
        }
    }

    //数据回调
    if(isset($message['MsgType'])){
        switch ($message['MsgType']) {
            case 'event':
                return '事件消息';//详情 https://work.weixin.qq.com/api/doc/90001/90143/90376#%E5%88%A0%E9%99%A4%E6%88%90%E5%91%98%E4%BA%8B%E4%BB%B6
                break;
            case 'text':
                return '文本消息';//详情 https://work.weixin.qq.com/api/doc/90001/90143/90375#%E5%9B%BE%E7%89%87%E6%B6%88%E6%81%AF
                break;
            case 'image':
                return '图片消息';
                break;
                //等等...不再一一举例
            default:
                return '其他消息';
                break;
        }
    }

});
$response = $server->serve();
$response->send();
```
