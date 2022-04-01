# 产品图册

### 创建商品图册

```php
$params = [
  'description'=>'世界上最好的商品',
  'price'=>30000,
  'product_sn'=>'xxxxxxxx',
  'attachments'=>[
      [
          'type'=> 'image',
          'image'=> [
              'media_id'=> 'MEDIA_ID'
          ]
      ]
  ]
];

$app->product->createProductAlbum($params);
```

### 获取商品图册列表

```php
$app->product->getProductAlbums(int $limit, string $cursor);
```

### 获取商品图册

```php
$productId = 'productId';

$app->product->getProductAlbumDetails($productId);
```

### 删除商品图册

```php
$productId = 'productId';

$app->product->deleteProductAlbum($productId);
```
