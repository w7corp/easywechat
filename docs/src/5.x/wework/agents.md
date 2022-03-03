# 应用管理

>  企业微信在17年11月对 API 进行了大量的改动，应用管理部分已经没啥用了

应用管理是企业微信中比较特别的地方，因为它的使用是不基于应用的，或者说基于任何一个应用都能访问这些 API，所以在用法上是直接调用 work 实例的 `agent` 属性。

```php
$config = [
    ...
];

$app = Factory::work($config);
```

## 应用列表

```php
$agents = $app->agent->list(); // 测试拿不到内容
```

## 应用详情

```php
$agents = $app->agent->get($agentId); // 只能传配置文件中的 id，API 改动所致
```

## 设置应用

```php
$agents = $app->agent->set($agentId, ['foo' => 'bar']);
```

## 设置工作台自定义展示

### 模版类型数据结构

可以通过接口配置展示类型。具体可设置:

- 关键数据型
- 图片型
- 列表型
- webview型

> 官方文档
> https://open.work.weixin.qq.com/api/doc/90000/90135/92535

### 设置应用在工作台展示的模版

```php
$params = [
    'agentid' => 1000005,
    'type' => 'image', //展示类型
    'image' => [
        'url' => 'xxxx',
        'jump_url' => 'http://www.qq.com',
        'pagepath' => 'pages/index'
    ],
    'replace_user_data' => true
];

$agents->agent_workbench->setWorkbenchTemplate(array $params);
```

### 获取应用在工作台展示的模版

```php
$agentId = 100005;

$agents->agent_workbench->getWorkbenchTemplate(int $agentId);
```


### 设置应用在用户工作台展示的数据

```php
$params = [
    'agentid' => 1000005,
    'userid' => 'test', //员工id
    'type' => 'keydata', //展示类型
    'keydata' => [
        'items' => [
            [
                'key' => '待审批',
                'data' => '2',
                'jump_url' => 'http://www.qq.com',
                'pagepath' => 'pages/index'
            ],
            [
                'key' => '带批阅作业',
                'data' => '4',
                'jump_url' => 'http://www.qq.com',
                'pagepath' => 'pages/index'
            ],
            [
                'key' => '成绩录入',
                'data' => '45',
                'jump_url' => 'http://www.qq.com',
                'pagepath' => 'pages/index'
            ],
            [
                'key' => '综合评价',
                'data' => '98',
                'jump_url' => 'http://www.qq.com',
                'pagepath' => 'pages/index'
            ]
        ]
    ]
];

$agents->agent_workbench->setWorkbenchData(array $params);
```