# 通讯录

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx', // 通讯录的 secret
    //...
];

$contacts = Factory::work($config);
```

## 成员管理
### 创建成员

```php
$data = [
    "userid" => "overtrue",
    "name" => "超哥",
    "english_name" => "overtrue"
    "mobile" => "1818888888",
];
$contacts->user->create($data);
```

### 读取成员

```php
$contacts->user->get('overtrue');
```

### 更新成员

```php
$contacts->user->update('overtrue', [
    "isleader": 0,
    'position' => 'PHP 酱油工程师',
    //...
]);
```

### 删除成员

```php
$contacts->user->delete('overtrue');
// 或者删除多个
$contacts->user->delete(['overtrue', 'zhangsan', 'wangwu']);
```

### 获取部门成员

```php
$contacts->user->getDepartmentUsers($departmentId);
// 递归获取子部门下面的成员
$contacts->user->getDepartmentUsers($departmentId, true);
```

### 获取部门成员详情

```php
$contacts->user->getDetailedDepartmentUsers($departmentId);
// 递归获取子部门下面的成员
$contacts->user->getDetailedDepartmentUsers($departmentId, true);
```

### 用户 ID 转为 openid

```php
$contacts->user->userIdToOpenid($userId);
// 或者指定应用 ID
$contacts->user->userIdToOpenid($userId, $agentId);
```

### openid 转为用户 ID

```php
$contacts->user->openidToUserId($openid);
```

### 手机号转为用户 ID

```php
$contacts->user->mobileToUserId($mobile);
```

### 二次验证

企业在成员验证成功后，调用如下接口即可让成员加入成功

```php
$contacts->user->accept($userId);
```

### 邀请成员

企业可通过接口批量邀请成员使用企业微信，邀请后将通过短信或邮件下发通知。

```php
$params = [
    'user' => ['UserID1', 'UserID2', 'UserID3'],    // 成员ID列表, 最多支持1000个
    'party' => ['PartyID1', 'PartyID2'],            // 部门ID列表，最多支持100个
    'tag' => ['TagID1', 'TagID2'],                  // 标签ID列表，最多支持100个
];

$contacts->user->invite($params);
```

> `user`, `party`, `tag` 三者不能同时为空

### 获取邀请二维码

```php
$sizeType = 1;  // qrcode尺寸类型，1: 171 x 171; 2: 399 x 399; 3: 741 x 741; 4: 2052 x 2052

$contacts->user->getInvitationQrCode($sizeType);
```

## 部门管理

### 创建部门

```php
$contacts->department->create([
        'name' => '广州研发中心',
        'parentid' => 1,
        'order' => 1,
        'id' => 2,
    ]);
```

### 更新部门

```php
$contacts->department->update($id, [
        'name' => '广州研发中心',
        'parentid' => 1,
        'order' => 1,
    ]);
```

### 删除部门

```php
$contacts->department->delete($id);
```

### 获取部门列表

```php
$contacts->department->list();
// 获取指定部门及其下的子部门
$contacts->department->list($id);
```

## 标签管理

### 创建标签

```php
$contacts->tag->create($tagName, $tagId);
```

### 更新标签名字

```php
$contacts->tag->update($tagId, $tagName);
```

### 删除标签

```php
$contacts->tag->delete($tagId);
```

### 获取标签列表

```php
$contacts->tag->list();
```

### 获取标签成员(标签详情)

```php
$contacts->tag->get($tagId);
```

### 增加标签成员

```php
$contacts->tag->tagUsers($tagId, [$userId1, $userId2, ...]);

// 指定部门
$contacts->tag->tagDepartments($tagId, [$departmentId1, $departmentId2, ...]);
```


### 删除标签成员

```php
$contacts->tag->untagUsers($tagId, [$userId1, $userId2, ...]);

// 指定部门
$contacts->tag->untagDepartments($tagId, [$departmentId1, $departmentId2, ...]);
```




