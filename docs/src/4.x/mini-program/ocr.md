# OCR 文字识别

小程序OCR功能提供各种文字识别能力，包括身份证、银行卡、驾驶证等证件识别。

## 获取实例

```php
$ocr = $app->ocr;
```

## 身份证识别

```php
// 身份证正面
$ocr->idcard($mediaId, 'photo');

// 身份证反面  
$ocr->idcard($mediaId, 'reverse');
```

## 银行卡识别

```php
$ocr->bankcard($mediaId);
```

## 驾驶证识别

```php
$ocr->driving($mediaId);
```

## 行驶证识别

```php
$ocr->drivingLicense($mediaId);
```

## 营业执照识别

```php
$ocr->businessLicense($mediaId);
```

## 通用印刷体识别

```php
$ocr->printedText($mediaId);
```