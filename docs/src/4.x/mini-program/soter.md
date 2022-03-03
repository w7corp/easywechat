# 生物认证

## 生物认证秘钥签名验证

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/soter/soter.verifySignature.html

```php
$app->soter->verifySignature($openid, $json, $signature);
```

返回值示例:
```json
{
    "is_ok": true
}
```

参数说明:

> - string $openid - 用户 openid
> - string $json - 通过 [wx.startSoterAuthentication](https://developers.weixin.qq.com/miniprogram/dev/api/open-api/soter/wx.startSoterAuthentication.html) 成功回调获得的 resultJSON 字段
> - string $signature - 通过 [wx.startSoterAuthentication](https://developers.weixin.qq.com/miniprogram/dev/api/open-api/soter/wx.startSoterAuthentication.html) 成功回调获得的 resultJSONSignature 字段