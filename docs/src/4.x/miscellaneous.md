# 其它

## 实用工具方法

EasyWeChat 4.x 为各个模块提供了实用的工具方法，通过 `getUtils()` 方法访问。

### 公众号工具

```php
$app = new \EasyWeChat\OfficialAccount\Application($config);
$utils = $app->getUtils();

// 构建 JSSDK 配置
$config = $utils->buildJsSdkConfig($url, $jsApiList, $openTagList, $debug);
```

### 小程序工具

```php
$app = new \EasyWeChat\MiniApp\Application($config);
$utils = $app->getUtils();

// 根据 code 获取 session 信息
$session = $utils->codeToSession($code);

// 解密用户数据
$userData = $utils->decryptSession($sessionKey, $iv, $encryptedData);
```

### 企业微信工具

```php
$app = new \EasyWeChat\Work\Application($config);
$utils = $app->getUtils();

// 构建 JSSDK 配置
$config = $utils->buildJsSdkConfig($url, $jsApiList, $openTagList, $debug, $beta);

// 构建企业微信应用的 JSSDK 配置
$agentConfig = $utils->buildJsSdkAgentConfig($url, $jsApiList, $openTagList, $debug);
```

### 微信支付工具

```php
$app = new \EasyWeChat\Pay\Application($config);
$utils = $app->getUtils();

// 构建微信内支付配置（WeixinJSBridge）
$bridgeConfig = $utils->buildBridgeConfig($prepayId, $appId);

// 构建 JSSDK 支付配置
$sdkConfig = $utils->buildSdkConfig($prepayId, $appId);

// 构建小程序支付配置
$miniAppConfig = $utils->buildMiniAppConfig($prepayId, $appId);

// 构建 APP 支付配置
$appConfig = $utils->buildAppConfig($prepayId, $appId);

// RSA 公钥加密
$encrypted = $utils->encryptWithRsaPublicKey($plaintext, $serial);

// 创建 V2 签名
$signature = $utils->createV2Signature($params);
```

## HTTP 客户端使用

所有模块都提供了 HTTP 客户端用于直接调用微信 API：

```php
// 获取客户端
$client = $app->getClient();

// GET 请求
$response = $client->get('/cgi-bin/user/list', [
    'query' => ['next_openid' => $nextOpenId]
]);

// POST JSON 请求
$response = $client->postJson('/cgi-bin/user/info/updateremark', [
    'openid' => $openId,
    'remark' => $remark
]);

// 处理响应
$data = $response->toArray();
```