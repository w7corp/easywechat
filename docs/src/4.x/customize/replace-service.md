# 自定义服务模块

由于使用了容器模式来组织各模块的实例，意味着你可以比较容易的替换掉已经有的服务，以公众号服务为例：

```php

<...>

$app = Factory::officialAccount($config);

$app->rebind('request', new MyCustomRequest(...)); 
```

这里的 `request` 为 SDK 内部服务名称。
