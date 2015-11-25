<?php

/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-11-1
 * Time: 下午5:12
 */
namespace Overtrue\Wechat\Test\Shop\Data;


use Overtrue\Wechat\Media;
use Overtrue\Wechat\Shop\Data\Product;
use Overtrue\Wechat\Test\TestBase;
use Symfony\Component\Yaml\Yaml;

class ProductTest extends TestBase
{

    protected $categoryId = '536891949';

    public function testSetBaseAttr()
    {
        $product = new Product();

        $data = $product->setBaseAttr('主图',array('图一','图二'),null,'name',$this->categoryId);
        $this->assertInstanceOf(Product::class,$data);

        $product = new Product(true);
        $data = $product->setBaseAttr('主图',array('图一','图二'));
        $this->assertInstanceOf(Product::class,$data);
    }

    public function testSetDetail()
    {
        $product = new Product();
        $data = $product->setDetail('img','图片');

        $this->assertInstanceOf(Product::class,$data);

        $data = $product->setDetail('text','文字');
        $this->assertInstanceOf(Product::class,$data);

    }

    public function testSetProperty()
    {

        $product = new Product();
        $data = $product->setProperty('id','vid');
        $this->assertInstanceOf(Product::class,$data);

    }


    public function testSetSkuInfo()
    {

        $product = new Product();
        $data = $product->setSkuInfo('id',array('vid','vid'));
        $this->assertInstanceOf(Product::class,$data);
    }


    public function testSetSkuList()
    {

        $product = new Product();
        $data = $product->setSkuList('原价','微信价','sku_ico','sku 库存',array('id'=>'vid','id1'=>'vid1'));
        $this->assertInstanceOf(Product::class,$data);
    }

    public function testSetAttrext()
    {
        $product = new Product();
        $data = $product->setAttrext(0,1,1,1);
        $this->assertInstanceOf(Product::class,$data);
    }


    public function testSetLocation()
    {
        $product = new Product();
        $data = $product->setLocation('浙江省','杭州市','滨江区阿里园');
        $this->assertInstanceOf(Product::class,$data);
    }

    public function testSetDeliveryInfo()
    {
        $product = new Product();
        $data = $product->setDeliveryInfo(1,'400184180');
        $this->assertInstanceOf(Product::class,$data);
    }

    public function testSetExpress()
    {
        $product = new Product();
        $data = $product->setExpress('id','price');
        $this->assertInstanceOf(Product::class,$data);
    }


    public function testGetData()
    {

        $media = new Media($this->config->appId,$this->config->appSecret);
        $image = $media->lists('image');

        //未上架
        $product = new Product();
        $product->setBaseAttr($image['item'][0]['url'],array($image['item'][0]['url'],$image['item'][0]['url']),null,'商品名',$this->categoryId)
            ->setDetail('text','text')
            ->setDetail('img',$image['item'][0]['url']);

        $testData = Yaml::parse(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'Data.yml'));

        foreach ($testData['Overtrue\Wechat\Test\Shop\ProductTest']['testGetProperty'] as $value) {
            $product->setProperty($value['id'],$value['property_value'][0]['id']);
        }

        foreach ($testData['Overtrue\Wechat\Test\Shop\ProductTest']['testGetSku'] as $value) {
            foreach ($value['value_list'] as $key => $valueList) {
                //此判断仅是为了 让子序列少点，实际程序按照自身的逻辑来
                if ($key % 5 == 0 && $key % 2 == 0) {
                    $skuList[] = $valueList['id'];
                }

            }
            $skuInfo[] = array('id'=>$value['id'],'vid'=>$skuList);
            $product->setSkuInfo($value['id'],$skuList);
        }

        foreach ($skuInfo[0]['vid'] as $vid) {
            foreach ($skuInfo[1]['vid'] as $vid1) {
                $product->setSkuList(100, 20, $image['item'][0]['url'], 100, array(array($skuInfo[0]['id'], $vid), array($skuInfo[1]['id'], $vid1)));
            }
        }

        $data = $product->setAttrext(0,1,1,1)
            ->setLocation('浙江省','杭州市','滨江区阿里园')
            ->setDeliveryInfo(1,'400184180')
            ->toArray();
        $this->assertTrue(is_array($data));

//        //以上架
//        $product = new Product(true);
//        $data = $product->setBaseAttr('main_img',array('img','img'))
//            ->setDetail('text','text')
//            ->setDetail('img','image')
//            //->setProperty('id','vid')
//            //->setProperty('id','vid')
//            ->setSkuInfo('id',array('vid','vid'))
//            //统一售价
//            //->setSkuList('原价','微信价','sku_ico','sku 库存');
//            //设置ｓｋｕ售价
//            ->setSkuList('原价','微信价','sku_ico','sku 库存',array('id'=>'vid','id1'=>'vid1'))
//            ->setAttrext(0,1,1,1)
//            ->setLocation('浙江省','杭州市','滨江区阿里园')
//            ->setDeliveryInfo(1,'400184180')
//            ->toArray();
//        $this->assertTrue(is_array($data));


    }
}
