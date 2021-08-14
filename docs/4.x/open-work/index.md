# 企业微信第三方服务商

## 实例化

```php
<?php
use EasyWeChat\Factory;

$config = [
     'corp_id'              => '服务商的corpid',
     'secret'               => '服务商的secret，在服务商管理后台可见',
     'suite_id'             => '以ww或wx开头应用id',
     'suite_secret'         => '应用secret',
     'token'                => '应用的Token',
     'aes_key'              => '应用的EncodingAESKey',
     'reg_template_id'      => '注册定制化模板ID',
     'redirect_uri_install' => '安装应用的回调url（可选）', 
     'redirect_uri_single'  => '单点登录回调url （可选）', 
     'redirect_uri_oauth'   => '网页授权第三方回调url （可选）', 
     
];

$app = Factory::openWork($config);
```

