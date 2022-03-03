# 模板消息

## 获取小程序模板库标题列表

```
$app->template_message->list($offset, $count);
```

## 获取模板库某个模板标题下关键词库

```
$app->template_message->get($id);
```

## 组合模板并添加至帐号下的个人模板库

```
$app->template_message->add($id, $keywordIdList);
```

## 获取帐号下已存在的模板列表

```
$app->template_message->getTemplates($offset, $count);
```

## 删除帐号下的某个模板

```
$app->template_message->delete($templateId);
```

## 发送模板消息

```php
$app->template_message->send([
    'touser' => 'user-openid',
    'template_id' => 'template-id',
    'page' => 'index',
    'form_id' => 'form-id',
    'data' => [
        'keyword1' => 'VALUE',
        'keyword2' => 'VALUE2',
        // ...
    ],
]);
```
