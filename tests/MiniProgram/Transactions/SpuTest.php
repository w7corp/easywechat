<?php

namespace EasyWeChat\tests\MiniProgram\Transactions;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Tests\TestCase;

class SpuTest extends TestCase
{

    public function getApplication($queries = [], $content = null)
    {
        return new Application([]);
    }

    public function testAddProduct()
    {
        $app = $this->getApplication();
        //请参考微信官方文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/SPU/add_spu.html
        $params = [
            "out_product_id" => "",
            "title" => "",
            "path" => "",
            "head_img" => "",
            "qualification_pics" => "",
            "desc_info" => "",
            "third_cat_id" => "",
            "brand_id" => "",
            "skus" => "",
        ];
        $result = $app->mini_program->product->addProduct($params);
        $this->assertEquals(0, $result['errcode']);
    }
    public function testGetProducts(){
        $app = $this->getApplication();
        //请参考微信官方文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/SPU/get_spu_list.html
        $params=[
            "status"=>"",
            "start_create_time"=>"",
            "end_create_time"=>"",
            "start_update_time"=>"",
            "end_update_time"=>"",
            "page"=>"",
            "page_size"=>"",
            "need_edit_spu"=>"",
        ];
        $result = $app->mini_program->product->getProducts($params);
        $this->assertEquals(0, $result['errcode']);
    }
    public function testUpdateProduct(){
        $app = $this->getApplication();
        //请参考微信官方文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/SPU/update_spu.html
        $params=[
            "out_product_id"=>"",
            "product_id"=>"",
            "title"=>"",
            "path"=>"",
            "head_img"=>"",
            "qualification_pics"=>"",
            "desc_info"=>"",
            "third_cat_id"=>"",
            "brand_id"=>"",
            "skus"=>"",
        ];
        $result = $app->mini_program->product->updateProduct($params);
        $this->assertEquals(0, $result['errcode']);
    }
}