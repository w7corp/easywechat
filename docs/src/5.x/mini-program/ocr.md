# OCR 文字识别

小程序OCR功能提供各种文字识别能力，包括身份证、银行卡、驾驶证等证件识别以及通用文字识别。

## 获取实例

```php
$ocr = $app->ocr;
```

## 身份证识别

识别身份证正反面信息：

```php
// 身份证正面
$result = $ocr->idcard($mediaId, 'photo');

// 身份证反面  
$result = $ocr->idcard($mediaId, 'reverse');
```

**参数说明：**
- `mediaId` string 图片的媒体ID
- `type` string 识别类型：'photo'(正面) 或 'reverse'(反面)

**返回结果（正面）：**
```json
{
    "type": "身份证正面",
    "name": "张三",
    "id": "110101199001011234",
    "addr": "北京市东城区...",
    "gender": "男",
    "nationality": "汉"
}
```

## 银行卡识别

识别银行卡信息：

```php
$result = $ocr->bankcard($mediaId);
```

**返回结果：**
```json
{
    "number": "6225881234567890"
}
```

## 驾驶证识别

识别驾驶证信息：

```php
$result = $ocr->driving($mediaId);
```

**返回结果：**
```json
{
    "id_num": "110101199001011234",
    "name": "张三",
    "nationality": "中国",
    "sex": "男",
    "address": "北京市...",
    "birth_date": "1990-01-01",
    "issue_date": "2020-01-01",
    "car_class": "C1",
    "valid_from": "2020-01-01",
    "valid_to": "2026-01-01"
}
```

## 行驶证识别

识别行驶证信息：

```php
$result = $ocr->drivingLicense($mediaId);
```

**返回结果：**
```json
{
    "vehicle_type": "小型汽车",
    "owner": "张三",
    "addr": "北京市...",
    "use_character": "非营运",
    "model": "长安牌SC1019...",
    "plate_num": "京A12345",
    "vin": "LDC613P23A1050312",
    "engine_num": "0123456",
    "register_date": "2020-01-01",
    "issue_date": "2020-01-01"
}
```

## 营业执照识别

识别营业执照信息：

```php
$result = $ocr->businessLicense($mediaId);
```

**返回结果：**
```json
{
    "reg_num": "91110101MA01A1B2C3",
    "serial": "12345678",
    "legal_representative": "张三",
    "enterprise_name": "北京测试公司",
    "type_of_organization": "有限责任公司",
    "address": "北京市...",
    "type_of_enterprise": "私营",
    "business_scope": "技术开发...",
    "registered_capital": "100万人民币",
    "paid_in_capital": "100万人民币",
    "valid_period": "2020-01-01至长期",
    "registered_date": "2020-01-01",
    "cert_position": {
        "pos": {
            "left_top": {"x": 155, "y": 191},
            "right_top": {"x": 725, "y": 157},
            "right_bottom": {"x": 743, "y": 512},
            "left_bottom": {"x": 164, "y": 539}
        }
    },
    "img_size": {"w": 966, "h": 728}
}
```

## 通用印刷体识别

识别图片中的印刷体文字：

```php
$result = $ocr->printedText($mediaId);
```

**返回结果：**
```json
{
    "items": [
        {
            "text": "腾讯",
            "pos": {
                "left_top": {"x": 575, "y": 519},
                "right_top": {"x": 744, "y": 519},
                "right_bottom": {"x": 744, "y": 532},
                "left_bottom": {"x": 575, "y": 532}
            }
        }
    ],
    "img_size": {"w": 1280, "h": 720}
}
```

## 完整示例

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    // ...
];

$app = Factory::miniProgram($config);
$ocr = $app->ocr;

// 识别身份证正面
$result = $ocr->idcard('media_id_123', 'photo');

if ($result['errcode'] === 0) {
    $name = $result['name'];
    $idNumber = $result['id'];
    $address = $result['addr'];
    
    echo "姓名：{$name}\n";
    echo "身份证号：{$idNumber}\n";
    echo "地址：{$address}\n";
}

// 识别银行卡
$result = $ocr->bankcard('media_id_456');
if ($result['errcode'] === 0) {
    $cardNumber = $result['number'];
    echo "银行卡号：{$cardNumber}\n";
}
```

## 注意事项

1. 图片要求：格式支持PNG、JPG、JPEG、BMP，大小不超过1M
2. 证件图片要求清晰，避免模糊、反光、阴影等情况
3. 每个小程序每天有一定的免费识别额度，超出部分按量收费
4. 建议在客户端先对图片进行预处理，如裁剪、旋转等，以提高识别准确率