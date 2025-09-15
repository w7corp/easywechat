# 微盘

企业微信微盘功能提供企业文件存储和管理能力，支持空间管理、文件操作等功能。

## 获取实例

```php
$wedrive = $app->wedrive;
```

## 空间管理

### 新建空间

```php
$authInfo = [
    [
        'type' => 1,  // 权限类型：1-成员 2-部门
        'userid' => 'zhangsan',
        'auth' => 1   // 权限：1-可下载 2-可预览 3-可编辑 4-可管理
    ],
    [
        'type' => 2,
        'departmentid' => 2,
        'auth' => 2
    ]
];

$result = $wedrive->space->create(
    'admin_userid',      // 操作者userid
    '项目资料空间',       // 空间名称
    $authInfo            // 权限信息
);
```

### 获取空间信息

```php
$result = $wedrive->space->get('admin_userid', 'space_id');
```

### 重命名空间

```php
$result = $wedrive->space->rename('admin_userid', 'space_id', '新空间名称');
```

### 解散空间

```php
$result = $wedrive->space->dismiss('admin_userid', 'space_id');
```

## 文件管理

### 上传文件

```php
$result = $wedrive->file->upload([
    'userid' => 'zhangsan',
    'spaceid' => 'space_id',
    'fatherid' => 'parent_folder_id',  // 父文件夹ID，根目录为空
    'file_name' => '项目文档.docx',
    'file_base64_content' => base64_encode(file_get_contents('/path/to/file.docx'))
]);
```

### 新建文件夹

```php
$result = $wedrive->file->createFolder([
    'userid' => 'zhangsan',
    'spaceid' => 'space_id',
    'fatherid' => '',  // 父目录ID，根目录为空
    'folder_name' => '2023年度报告'
]);
```

### 获取文件列表

```php
$result = $wedrive->file->list([
    'userid' => 'zhangsan',
    'spaceid' => 'space_id',
    'fatherid' => '',  // 目录ID，根目录为空
    'sort_type' => 1,  // 排序方式：1-名称 2-修改时间 3-大小
    'start' => 0,
    'limit' => 50
]);
```

### 下载文件

```php
$result = $wedrive->file->download('zhangsan', 'file_id');
```

### 移动文件

```php
$result = $wedrive->file->move([
    'userid' => 'zhangsan',
    'spaceid' => 'space_id',
    'fileid' => 'file_id',
    'replace' => false,  // 是否覆盖同名文件
    'fatherid' => 'target_folder_id'
]);
```

### 删除文件

```php
$result = $wedrive->file->delete('zhangsan', 'file_id');
```

### 重命名文件

```php
$result = $wedrive->file->rename([
    'userid' => 'zhangsan',
    'fileid' => 'file_id',
    'new_name' => '新文件名.docx'
]);
```

## 权限管理

### 设置文件权限

```php
$authInfo = [
    [
        'type' => 1,
        'userid' => 'lisi',
        'auth' => 2  // 可预览
    ]
];

$result = $wedrive->file->setAuth([
    'userid' => 'zhangsan',
    'fileid' => 'file_id',
    'auth_info' => $authInfo
]);
```

### 获取文件权限

```php
$result = $wedrive->file->getAuth('zhangsan', 'file_id');
```