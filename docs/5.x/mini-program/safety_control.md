# 安全风控

> 微信文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/safety-control-capability/riskControl.getUserRiskRank.html

> tips: 根据提交的用户信息数据获取用户的安全等级 risk_rank，无需用户授权。

## 获取用户的安全等级

```php
$app->risk_control->getUserRiskRank([
	'appid' => 'wx311232323',
	'openid' => 'oahdg535ON6vtkUXLdaLVKvzJdmM',
	'scene' => 1,
	'client_ip' => '12.234.134.2',
]);
```