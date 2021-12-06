<?php

namespace EasyWeChat\tests\MiniProgram\Transactions;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Tests\TestCase;

class TransactionsTest extends TestCase
{
    public function getApplication($queries = [], $content = null)
    {
        return new Application([]);
    }

    public function testAuditBrand()
    {

        $app = $this->getApplication();
        //请参考微信官方文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/audit/audit_brand.html
        $params["audit_req"] = [
            "license" => "",
            "brand_info" => [
                "brand_audit_type" => "",
                "trademark_type" => "",
                "brand_management_type" => "",
                "commodity_origin_type" => "",
                "brand_wording" => "",
                "sale_authorization" => "",
                "trademark_registration_certificate" => "",
                "trademark_change_certificate" => "",
                "trademark_registrant" => "",
                "trademark_registrant_nu" => "",
                "trademark_authorization_period" => "",
                "trademark_registration_application" => "",
                "trademark_applicant" => "",
                "trademark_application_time" => "",
                "imported_goods_form" => "",
            ]
        ];
        $result = $app->mini_program->transactions->auditBrand($params);
        $this->assertEquals(0, $result['errcode']);
    }
    public function testAuditCategory(){
        $app = $this->getApplication();
        //请参考微信官方文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/audit/audit_category.html
        $params["audit_req"] = [
            "license" => "",
            "category_info" => [
                "level1" => "",
                "level2" => "",
                "level3" => "",
                "certificate" => "",
            ]
        ];
        $result = $app->mini_program->transactions->auditCategory($params);
        $this->assertEquals(0, $result['errcode']);
    }

}