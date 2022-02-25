# 服务端

企业微信服务端推送和公众号一样，请参考：[公众号：服务端](../official-account/server.md)

## 第三方平台推送事件

企业微信数据推送的有以下事件：

- 通讯录变更（Event） `change_contact`
  - ChangeType
    - 成员变更
      - 新增成员 `create_user`
      - 更新成员 `update_user`
      - 删除成员 `delete_user`
    - 部门变更
      - 新增部门 `create_party`
      - 更新部门 `update_party`
      - 删除部门 `delete_party`
    - 标签变更
      - 成员标签变更 `update_tag`
- 批量任务执行完成 `batch_job_result`

## 自定义消息处理器

> *消息处理器详细说明见：公众号开发 - 服务端一节*

```php
// 处理通讯录变更事件（包括成员变更、部门变更、成员标签变更）
$server->handleContactChanged(callable | string $handler);

// 处理任务执行完成事件
$server->handleBatchJobsFinished(callable | string $handler);

// 成员变更事件
$server->handleUserCreated(callable | string $handler);
$server->handleUserUpdated(callable | string $handler);
$server->handleUserDeleted(callable | string $handler);

// 部门变更事件
$server->handlePartyCreated(callable | string $handler);
$server->handlePartyUpdated(callable | string $handler);
$server->handlePartyDeleted(callable | string $handler);

// 成员标签变更事件
$server->handleUserTagUpdated(callable | string $handler);
```
