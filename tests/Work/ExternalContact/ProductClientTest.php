<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\ExternalContact;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\ExternalContact\ProductClient;

/**
 * Class ProductClientTest.
 *
 * @package EasyWeChat\Tests\Work\ExternalContact
 *
 * @author 读心印 <aa24615@qq.com>
 */
class ProductClientTest extends TestCase
{
    /**
     * testCreateProductAlbum.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testCreateProductAlbum()
    {
        $client = $this->mockApiClient(ProductClient::class);

        $params = [
            'description' => '世界上最好的商品',
            'price' => 30000,
            'product_sn' => 'xxxxxxxx',
            'attachments' => [
                [
                    'type' => 'image',
                    'image' => [
                    'media_id' => 'MEDIA_ID'
                    ]
                ]
            ]
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/add_product_album', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->createProductAlbum($params));
    }

    /**
     * testUpdateProductAlbum.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testUpdateProductAlbum()
    {
        $client = $this->mockApiClient(ProductClient::class);

        $params = [
            'product_id' => 'test_id',
            'description' => '世界上最好的商品',
            'price' => 30000,
            'product_sn' => 'xxxxxxxx',
            'attachments' => [
                [
                    'type' => 'image',
                    'image' => [
                        'media_id' => 'MEDIA_ID'
                    ]
                ]
            ]
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/update_product_album', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->updateProductAlbum($params));
    }

    /**
     * testGetProductAlbums.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testGetProductAlbums()
    {
        $client = $this->mockApiClient(ProductClient::class);

        $params = [
            'limit' => 50,
            'cursor' => 'xxx123',
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_product_album_list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getProductAlbums(50, 'xxx123'));
    }

    /**
     * testGetProductAlbum.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testGetProductAlbum()
    {
        $client = $this->mockApiClient(ProductClient::class);

        $params = [
            'product_id' => 'test_id'
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_product_album', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getProductAlbumDetails('test_id'));
    }

    /**
     * testDeleteProductAlbum.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testDeleteProductAlbum()
    {
        $client = $this->mockApiClient(ProductClient::class);

        $params = [
            'product_id' => 'test_id'
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/delete_product_album', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->deleteProductAlbum('test_id'));
    }
}
