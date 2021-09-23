<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Kernel\Support;

use EasyWeChat\Kernel\Support\AesGcm;
use EasyWeChat\Tests\TestCase;

class AesGcmTest extends TestCase
{
    public function test_it_can_encrypt_and_decrypt()
    {
        // 感谢大佬 @TheNorthMemory 提供的测试数据
        $key = '5YI2YwEdV56hCsNEoOGEeL17vRFcz3i6';
        $nonce = 'katvtHDDPxkw';
        $aad = 'transaction';
        $plaintext = '{"transaction_id":"1217752501201407033233368018","amount":{"payer_total":100,"total":100,"currency":"CNY","payer_currency":"CNY"},"mchid":"1230000109","trade_state":"SUCCESS","bank_type":"CMC","promotion_detail":[{"amount":100,"wechatpay_contribute":0,"coupon_id":"109519","scope":"GLOBAL","merchant_contribute":0,"name":"单品惠-6","other_contribute":0,"currency":"CNY","stock_id":"931386","goods_detail":[{"goods_remark":"商品备注信息","quantity":1,"discount_amount":1,"goods_id":"M1006","unit_price":100},{"goods_remark":"商品备注信息","quantity":1,"discount_amount":1,"goods_id":"M1006","unit_price":100}]},{"amount":100,"wechatpay_contribute":0,"coupon_id":"109519","scope":"GLOBAL","merchant_contribute":0,"name":"单品惠-6","other_contribute":0,"currency":"CNY","stock_id":"931386","goods_detail":[{"goods_remark":"商品备注信息","quantity":1,"discount_amount":1,"goods_id":"M1006","unit_price":100},{"goods_remark":"商品备注信息","quantity":1,"discount_amount":1,"goods_id":"M1006","unit_price":100}]}],"success_time":"2018-06-08T10:34:56+08:00","payer":{"openid":"oUpF8uMuAJO_M2pxb1Q9zNjWeS6o"},"out_trade_no":"1217752501201407033233368018","appid":"wxd678efh567hg6787","trade_state_desc":"支付成功","trade_type":"MICROPAY","attach":"自定义数据","scene_info":{"device_id":"013467007045764"}}';
        $ciphertext = 'LsCtZf91SEdVjdVNHtl6cnmrMz6atFZbsF0cwQQmgrTSb4WOey3uaKPqKbVb3E+9bT65ND/vJDD7/kLBnqOf8j2niXJ3vtdx11dpAG9yevfWdD8My6k9moQ+uuoLq9D1LVM4/QFGUYU68mKZr5FsPjWlImj06vg010LWzNe24cInjPNfelvfsz1xDKVpMwC1dy4ANPt4ZOCmch/pfezLrg13f/aTs8Rs9v4v+B1jqme9oyUnSVFOOU19gCPvyvCLSVbvf3Ng0noWHKDh2IbA7tozFDkZr8q9Y9f9igfCUq4W4LCnyhIyidqYr17DM8O0I+JlAf+Awf0xFYRMDckQItWAsYp/a11Z3wZ4pEurRlL8Dvz1PsZU9X4ZmCtxfvYVND+b1Xf4zWJV4e9atODn9JIXg8ENmGASrxG64hOQI4al79DT2kIoWSy7SmdyJX0ZI6MILD+x4f5gi4GAT7lpw0ufXK73YXIRfvzEU3EsJDc72AHAsACTdWosSOmLejB2W0Saf27HqDxGOvb2IFaIlwVgL7g7hCItbFJ0MV3X7clwU0V35T+hF5lrrB9OSH0w0GWeRNZmWjeR+EQX2WJWB0JnG7ZKCtxkMhPcCThze92TkIJsCHj0c2uCOru37hMXvce3qJNZSKpMkperGia4HeqVRI+Zn+qc1PCfK+1m8wqdnROMzEa8U3Gw9Exx0CuYff6NGmIMQedr6IlBcJpM8loR1Q3b5ea2oI4lGy+3akXT70QkVRExIcubvPsCzf8r14+rgbonKCkGeAfSiIzRzW7u9HkTSzcZPDQqzMtaIsYVLx/b+eQ0tuGaYbLAfTO0yn+MJx+EQMAPi31qTAggmmSZOudvZHal2/azn12+dSF1Im1KZdddAFcTahKcu9Iu4qZo7q8XvOHuSLjSxh6aAWij6c4HbdxgxDw5zkk3GZtqG+u977HlPDgnJCDTvbTvQ7vrnptBzkXJ3QehJ8AlNLdW3GXXSF3ZkOPYNYnlrbEIpPu255nNj0aTURH4UXQgO+LCjy53bCEumlSoO91BQ1++WSBVnJK9xm5yuBDkjFSZjwQ27IM3V66db3766QUnUG8gnqASMzXQ+eNTAUMTNW8NC7ccQ1Z2YCsDVhEH7fDzbSuTExIV+/fgIE4Zpn7r+Ry+AnTRn92CQRW1ri8+hdL60uQDiBma1fovMNPX/cVrNwPF2r+b12Fp8KLF51572KCy487nj2+1OhzF8tbe8ZPLQRvv2bHKJnubQDKHP607KdbZMMPrl9BfpTQYVUKwo1GAjytizlQpVvWf4+PVgDjdys84tiw+iS9/HStC9NatcRZqXn4YR9c69ICaCqiHKEV9S7EPEI73nmb1o/Jl+Pnl/wul+eOtK5X3LY+n5Rh+WciCL4zZG64lsUGzRgFprxoJBqDJFFydVTzKktQT2aoHiUpk9j4Vc3fcmYru0CfUBi5qhi2bKr8+PwWWKgS1mPeU3o/cqkBUqSAXTjewq298351pQ7LVSadp+UXR5je+Nal5OfCVCAVyUB4PVhRTQLPcubRLwYRh/+1LLL/xizgUf6rCOcYNZQ3MnCUCdgVKE4IftSmFQwE91fdVoJQUbrsNuNchG26681XekJVh70iLhzmkYaSdE69WPfN01R8B4uQziY/VHLztg60F9vEIZs5DxPj2lafiwDGTk+cSH/VF2DC+A/8mJlqZCMEn2kv1SDg1i0aV74MJn6EDkI2NyahXAzvEEpdUDG9nRtRqSy9DA/E5Ae0HV0jqWXSGYSrXOT07PNqCxgzfgXE/oEGuGTb/30rD7dU=';

        // encrypt
        $this->assertSame($ciphertext, AesGcm::encrypt($plaintext, $key, $nonce, $aad));

        // decrypt
        $json = \json_decode(AesGcm::decrypt($ciphertext, $key, $nonce, $aad), true);

        $this->assertSame('1217752501201407033233368018', $json['transaction_id']);
    }
}
