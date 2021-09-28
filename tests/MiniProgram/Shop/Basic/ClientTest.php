<?php

namespace EasyWeChat\Tests\MiniProgram\Shop\Basic;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\MiniProgram\Shop\Basic\Client;

/**
 * 自定义版交易组件开放接口
 *    申请接入接口
 *
 * @package EasyWeChat\Tests\MiniProgram\Shop\Basic
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ClientTest extends TestCase
{
    /**
     * 获取商品类目
     */
    public function testGetCat()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/cat/get')
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getCat());
    }

    /**
     * 上传图片
     */
    public function testImgUpload()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpUpload('shop/img/upload', [
            'media' => '/foo/bar/image.jpg',
            ], [
            'resp_type' => 1,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->imgUpload('/foo/bar/image.jpg'));
    }

    /**
     * 上传品牌
     */
    public function testAuditBrand()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/audit/audit_brand', [
            'audit_req' => []
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->auditBrand([]));
    }

    /**
     * 类目审核
     */
    public function testAuditCategory()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/audit/audit_category', [
            'audit_req' => []
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->auditCategory([]));
    }

    /**
     * 获取审核结果
     */
    public function testAuditResult()
    {
        $client = $this->mockApiClient(Client::class);
        $auditId = '12341abc';

        $client->expects()->httpPostJson('shop/audit/result', [
            'audit_id' => $auditId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->auditResult($auditId));
    }

    /**
     * 获取小程序资质
     */
    public function testGetMiniAppCertificate()
    {
        $client = $this->mockApiClient(Client::class);
        $reqType = 3;

        $client->expects()->httpPostJson('shop/audit/get_miniapp_certificate', [
            'req_type' => $reqType
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getMiniAppCertificate($reqType));
    }
}
