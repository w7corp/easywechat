# 微盘

企业微信微盘功能提供企业文件存储和管理能力，支持空间管理、文件操作等功能。

## 获取实例

```php
$wedrive = $app->wedrive;
```

## 空间管理

### 新建空间

创建新的微盘空间：

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
    $authInfo,           // 权限信息
    0                    // 空间类型：0-普通 1-相册
);
```

**参数说明：**
- `userid` string 操作者的userid
- `spaceName` string 空间名称
- `authInfo` array 空间成员权限信息
- `spaceSubType` int 空间类型，0:普通 1:相册

**权限类型说明：**
- `type` int 权限类型：1-成员 2-部门
- `userid` string 成员userid（type=1时必填）
- `departmentid` int 部门ID（type=2时必填）
- `auth` int 权限级别：1-可下载 2-可预览 3-可编辑 4-可管理

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "spaceid": "s_3b5ca2b43e454b89a6b4c32e516e4e99"
}
```

### 获取空间信息

获取指定空间的详细信息：

```php
$result = $wedrive->space->get('admin_userid', 's_3b5ca2b43e454b89a6b4c32e516e4e99');
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "space_info": {
        "spaceid": "s_3b5ca2b43e454b89a6b4c32e516e4e99",
        "space_name": "项目资料空间",
        "auth_info": [
            {
                "type": 1,
                "userid": "zhangsan",
                "auth": 4,
                "create_time": 1635724800
            }
        ],
        "space_sub_type": 0,
        "space_capacity": 1073741824,
        "space_used": 268435456
    }
}
```

### 重命名空间

修改空间名称：

```php
$result = $wedrive->space->rename(
    'admin_userid',
    's_3b5ca2b43e454b89a6b4c32e516e4e99',
    '更新后的空间名称'
);
```

### 解散空间

删除指定的空间：

```php
$result = $wedrive->space->dismiss('admin_userid', 's_3b5ca2b43e454b89a6b4c32e516e4e99');
```

## 文件管理

### 上传文件

上传文件到指定空间：

```php
$result = $wedrive->file->upload([
    'userid' => 'zhangsan',
    'spaceid' => 's_3b5ca2b43e454b89a6b4c32e516e4e99',
    'fatherid' => 'parent_folder_id',  // 父文件夹ID，根目录为空
    'file_name' => '项目文档.docx',
    'file_base64_content' => base64_encode(file_get_contents('/path/to/file.docx'))
]);
```

**参数说明：**
- `userid` string 操作者userid
- `spaceid` string 空间ID
- `fatherid` string 父文件夹ID，根目录时为空
- `file_name` string 文件名
- `file_base64_content` string 文件内容的base64编码

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "fileid": "f_3b5ca2b43e454b89a6b4c32e516e4e99"
}
```

### 新建文件夹

在指定位置创建文件夹：

```php
$result = $wedrive->file->createFolder([
    'userid' => 'zhangsan',
    'spaceid' => 's_3b5ca2b43e454b89a6b4c32e516e4e99',
    'fatherid' => '',  // 父目录ID，根目录为空
    'folder_name' => '2023年度报告'
]);
```

### 获取文件列表

获取指定目录下的文件和文件夹列表：

```php
$result = $wedrive->file->list([
    'userid' => 'zhangsan',
    'spaceid' => 's_3b5ca2b43e454b89a6b4c32e516e4e99',
    'fatherid' => '',  // 目录ID，根目录为空
    'sort_type' => 1,  // 排序方式：1-名称 2-修改时间 3-大小
    'start' => 0,      // 起始位置
    'limit' => 50      // 返回数量
]);
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "has_more": false,
    "next_start": 0,
    "file_list": [
        {
            "fileid": "f_3b5ca2b43e454b89a6b4c32e516e4e99",
            "file_name": "项目文档.docx",
            "spaceid": "s_3b5ca2b43e454b89a6b4c32e516e4e99",
            "fatherid": "",
            "file_size": 1024000,
            "ctime": 1635724800,
            "mtime": 1635724800,
            "file_type": 1,
            "file_status": 1
        }
    ]
}
```

### 下载文件

获取文件下载链接：

```php
$result = $wedrive->file->download('zhangsan', 'f_3b5ca2b43e454b89a6b4c32e516e4e99');
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "download_url": "https://file.work.weixin.qq.com/xxx",
    "cookie_name": "wedrive_ticket",
    "cookie_value": "xxx"
}
```

### 移动文件

移动文件或文件夹到指定位置：

```php
$result = $wedrive->file->move([
    'userid' => 'zhangsan',
    'spaceid' => 's_3b5ca2b43e454b89a6b4c32e516e4e99',
    'fileid' => 'f_3b5ca2b43e454b89a6b4c32e516e4e99',
    'replace' => false,  // 是否覆盖同名文件
    'fatherid' => 'target_folder_id'  // 目标文件夹ID
]);
```

### 删除文件

删除指定的文件或文件夹：

```php
$result = $wedrive->file->delete('zhangsan', 'f_3b5ca2b43e454b89a6b4c32e516e4e99');
```

### 重命名文件

修改文件或文件夹名称：

```php
$result = $wedrive->file->rename([
    'userid' => 'zhangsan',
    'fileid' => 'f_3b5ca2b43e454b89a6b4c32e516e4e99',
    'new_name' => '新文件名.docx'
]);
```

## 权限管理

### 设置文件权限

设置文件的访问权限：

```php
$authInfo = [
    [
        'type' => 1,
        'userid' => 'lisi',
        'auth' => 2  // 可预览
    ],
    [
        'type' => 2,
        'departmentid' => 3,
        'auth' => 1  // 可下载
    ]
];

$result = $wedrive->file->setAuth([
    'userid' => 'zhangsan',
    'fileid' => 'f_3b5ca2b43e454b89a6b4c32e516e4e99',
    'auth_info' => $authInfo
]);
```

### 获取文件权限

查询文件的权限设置：

```php
$result = $wedrive->file->getAuth('zhangsan', 'f_3b5ca2b43e454b89a6b4c32e516e4e99');
```

## 使用示例

### 项目文档管理

```php
use EasyWeChat\Factory;

$config = [
    'corp_id' => 'your-corp-id',
    'agent_id' => 'your-agent-id',
    'secret' => 'your-secret',
    // ...
];

$app = Factory::work($config);
$wedrive = $app->wedrive;

// 1. 创建项目空间
$authInfo = [
    [
        'type' => 1,
        'userid' => 'project_manager',
        'auth' => 4  // 项目经理有管理权限
    ],
    [
        'type' => 2,
        'departmentid' => 10,  // 开发部门
        'auth' => 3  // 可编辑
    ],
    [
        'type' => 2,
        'departmentid' => 20,  // 测试部门
        'auth' => 2  // 可预览
    ]
];

$space = $wedrive->space->create('admin', 'Alpha项目文档空间', $authInfo);

if ($space['errcode'] === 0) {
    $spaceId = $space['spaceid'];
    echo "空间创建成功: {$spaceId}\n";
    
    // 2. 创建文件夹结构
    $folders = ['需求文档', '设计文档', '开发文档', '测试文档'];
    
    foreach ($folders as $folderName) {
        $folder = $wedrive->file->createFolder([
            'userid' => 'project_manager',
            'spaceid' => $spaceId,
            'fatherid' => '',
            'folder_name' => $folderName
        ]);
        
        if ($folder['errcode'] === 0) {
            echo "文件夹创建成功: {$folderName}\n";
        }
    }
    
    // 3. 上传项目文档
    $docPath = '/path/to/project_requirements.docx';
    if (file_exists($docPath)) {
        $upload = $wedrive->file->upload([
            'userid' => 'project_manager',
            'spaceid' => $spaceId,
            'fatherid' => '',  // 先上传到根目录
            'file_name' => '项目需求文档.docx',
            'file_base64_content' => base64_encode(file_get_contents($docPath))
        ]);
        
        if ($upload['errcode'] === 0) {
            echo "文档上传成功: {$upload['fileid']}\n";
        }
    }
}
```

### 批量文件操作

```php
// 获取空间中的所有文件
$fileList = $wedrive->file->list([
    'userid' => 'admin',
    'spaceid' => $spaceId,
    'fatherid' => '',
    'limit' => 100
]);

if ($fileList['errcode'] === 0) {
    foreach ($fileList['file_list'] as $file) {
        echo "文件: {$file['file_name']} ";
        echo "大小: " . round($file['file_size'] / 1024, 2) . "KB ";
        echo "修改时间: " . date('Y-m-d H:i:s', $file['mtime']) . "\n";
        
        // 如果是旧文件（超过30天），移动到归档文件夹
        if ($file['mtime'] < strtotime('-30 days')) {
            $move = $wedrive->file->move([
                'userid' => 'admin',
                'spaceid' => $spaceId,
                'fileid' => $file['fileid'],
                'fatherid' => 'archive_folder_id',
                'replace' => false
            ]);
            
            if ($move['errcode'] === 0) {
                echo "  -> 已移动到归档文件夹\n";
            }
        }
    }
}
```

### 文件分享与权限控制

```php
// 为外部合作伙伴设置特定文件的预览权限
$shareAuth = [
    [
        'type' => 1,
        'userid' => 'partner_001',
        'auth' => 2  // 只能预览，不能下载
    ]
];

$setAuth = $wedrive->file->setAuth([
    'userid' => 'project_manager',
    'fileid' => $fileId,
    'auth_info' => $shareAuth
]);

if ($setAuth['errcode'] === 0) {
    // 获取下载链接供分享
    $download = $wedrive->file->download('partner_001', $fileId);
    
    if ($download['errcode'] === 0) {
        echo "分享链接: {$download['download_url']}\n";
        echo "访问凭证: {$download['cookie_value']}\n";
    }
}
```

## 注意事项

1. **存储限制**：每个企业的微盘空间有总容量限制
2. **文件大小**：单个文件上传大小有限制，大文件建议分片上传
3. **权限继承**：子文件夹会继承父文件夹的权限设置
4. **删除恢复**：删除的文件会进入回收站，可以恢复
5. **并发操作**：避免同时对同一文件进行多个操作

## 最佳实践

1. **合理规划空间结构**：按项目或部门创建独立空间
2. **权限最小化原则**：只给必要的访问权限
3. **定期清理**：定期清理不需要的文件，释放存储空间
4. **版本管理**：对重要文档进行版本控制
5. **备份策略**：重要文件建议进行额外备份

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 0 | 成功 |
| 40003 | 无效的UserID |
| 41006 | 缺少spaceid参数 |
| 41008 | 缺少fileid参数 |
| 85005 | 文件不存在 |
| 85006 | 空间不存在 |
| 85007 | 没有权限 |
| 85008 | 空间容量不足 |