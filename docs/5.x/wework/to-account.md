# 企微ID账号升级转换

:book:   [官方文档 - 企业微信帐号ID安全性全面升级 说明文档](https://open.work.weixin.qq.com/api/doc/90001/90143/95327)

> 注意: 以下接口仅限第三方服务商调用

```php
$config = [...];

$app = Factory::openWork($config);
$work = $app->work('授权企业的corp_id','授权企业的永久授权码');
```

### corpid转换

```php
$work->corp_group->getOpenCorpid(string $corpId);
```

### userid转换

```php
$work->corp_group->batchUseridToOpenUserid(array $useridList);
```

### external_userid转换

```php
$work->external_contact->getNewExternalUserid(array $externalUserIds);
```

### 设置迁移完成

```php
$work->external_contact->finishExternalUseridMigration(string $corpId);
```

### unionid查询external_userid

```php
$work->external_contact->unionidToexternalUserid3rd(string $unionid, string $openid, string $corpid = '');
```