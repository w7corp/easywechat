<?php

/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-11-1
 * Time: 下午5:10
 */
namespace Overtrue\Wechat\Test\Shop;


use Overtrue\Wechat\Image;
use Overtrue\Wechat\Shop\Product;
use Overtrue\Wechat\Test\TestBase;
use Symfony\Component\Yaml\Yaml;
use Overtrue\Wechat\Shop\Data\Product as ProductData;
use Overtrue\Wechat\Media;

class ProductTest extends TestBase
{

    public function testCreate()
    {

        $image = new Image($this->config->appId,$this->config->appSecret);
        $images = $image->upload(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Image'.DIRECTORY_SEPARATOR.'aa.jpg');

        $product = new Product($this->http);
        $response = $product->create(function(ProductData $product) use ($images)  {


            $product->setBaseAttr($images,array($images,$images),null,'商品名',536891949)
                ->setDetail('text','text')
                ->setDetail('img',$images);

            $testData = Yaml::parse(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'Data.yml'));

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
                    $product->setSkuList(100, 20, $images, 100, array(array($skuInfo[0]['id'], $vid), array($skuInfo[1]['id'], $vid1)));
                }
            }

            $product->setAttrext(0,1,1,1)
                ->setLocation('浙江省','杭州市','滨江区阿里园')
                ->setDeliveryInfo(1,'400184224');

            return $product;
        });

        $this->assertTrue(is_string($response));

        return $response;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate($productId)
    {
        $image = new Image($this->config->appId,$this->config->appSecret);
        $images = $image->upload(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Image'.DIRECTORY_SEPARATOR.'aa.jpg');

        //未上架的
        $product = new Product($this->http);
        $response = $product->update(function(ProductData $product) use ($images)  {

            $product->setBaseAttr($images,array($images,$images),null,'商品名',536891949)
                ->setDetail('text','text')
                ->setDetail('img',$images);

            $testData = Yaml::parse(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'Data.yml'));

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
                    $product->setSkuList(100, 20, $images, 100, array(array($skuInfo[0]['id'], $vid), array($skuInfo[1]['id'], $vid1)));
                }
            }

            $product->setAttrext(0,1,1,1)
                ->setLocation('浙江省','杭州市','滨江区阿里园')
                ->setDeliveryInfo(1,'400184224');

            return $product;

        },$productId);

        $this->assertTrue(is_string($response));

    }

    /**
     * @depends testCreate
     */
    public function testGet($productId)
    {
        
        $product = new Product($this->http);
        $response = $product->get($productId);
        $this->assertTrue(is_array($response));
    }

    public function testGetByStatus()
    {
        
        $product = new Product($this->http);
        $response = $product->getByStatus();
        $this->assertTrue(is_array($response));
    }

    /**
     * @depends testCreate
     */
    public function testUpdateStatus($productId)
    {
        
        $product = new Product($this->http);
        $response = $product->updateStatus($productId,0);
        $this->assertTrue($response);
    }

    public function testGetSub()
    {
        
        $product = new Product($this->http);
        $response = $product->getSub();

        $this->assertTrue(is_array($response));

        foreach ($response as $value) {
            $this->assertArrayHasKey('id',$value);
            $this->assertArrayHasKey('name',$value);
        }


        $product = new Product($this->http);
        $response = $product->getSub(537891948);

        $this->assertTrue(is_array($response));

        foreach ($response as $value) {
            $this->assertArrayHasKey('id',$value);
            $this->assertArrayHasKey('name',$value);
        }

    }


    public function testGetSku()
    {
        $cateId = '536891949';

        $product = new Product($this->http);
        $response = $product->getSku($cateId);
        $this->assertTrue(is_array($response));
    }

    public function testGetProperty()
    {
        $cateId = '536891949';

        $product = new Product($this->http);
        $response = $product->getProperty($cateId);
        $this->assertTrue(is_array($response));
    }

    /**
     * @depends testCreate
     */
    public function testDelete($productId)
    {

        $product = new Product($this->http);
        $response = $product->delete($productId);
        $this->assertTrue($response);
    }
}
