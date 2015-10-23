<?php
/**
 * LuckMoney.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author bontian <bontian@163.com>
 *
 */

namespace Overtrue\Wechat\Payment;

use Overtrue\Wechat\Exception;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Utils\SignGenerator;
use Overtrue\Wechat\Http;

class LuckMoney
{
    const API_SEND_LUCK_MONEY = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
    const API_SEND_GROUP_LUCK_MONEY = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack';
    const API_QUERY_LUCK_MONEY = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo';

    const TYPE_CASH_LUCK_MONEY  = 'CASH_LUCK_MONEY';   //红包类型，现金红包
    const TYPE_GROUP_LUCK_MONEY = 'GROUP_LUCK_MONEY';  //红包类型，裂变红包

    /**
     * 商户信息
     *
     * @var Business
     */
    protected $business;

    public function __construct(Business $business)
    {
        if (!is_null($business)) {
            $this->setBusiness($business);
        }
    }

    /**
     * 设置商户
     *
     * @param Business $business
     *
     * @return $this
     * @throws Exception
     */
    public function setBusiness(Business $business)
    {
        if (!is_null($business)) {
            $this->business = $business;
        }
        return $this;
    }

    /**
     * 发送红包
     *
     * <pre>
     * $data:
     * {
     *     "mch_billno": "198374613512",
     *     "send_name":"某某公司",
     *     "re_openid": "oJCvDjjQKx5LMtM_1kjK0gGQLsew",
     *     "total_amount": 1000,
     *     "wishing": "祝福语",
     *     "act_name": "活动名称",
     *     "total_num": 1,
     *     "remark": "红包备注"
     * }
     * </pre>
     *
     * @param array $data
     * @param int $type
     *
     * @return array
     */
    public function send(array $data, $type = self::TYPE_CASH_LUCK_MONEY)
    {
        $defaultParam['nonce_str'] = uniqid('pre_');
        $defaultParam['mch_id'] = $this->business->mch_id;
        $defaultParam['wxappid'] = $this->business->appid;

        if($type == self::TYPE_CASH_LUCK_MONEY) {
            $defaultParam['client_ip'] = $_SERVER['REMOTE_ADDR'];
        }

        if($type == self::TYPE_GROUP_LUCK_MONEY){
            $defaultParam['amt_type'] = 'ALL_RAND';
        }

        $param = array_merge($data, $defaultParam);
        $signGenerator = new SignGenerator($param);
        $signGenerator->onSortAfter(function(SignGenerator $that) {
            $that->key = $this->business->mch_key;
        });

        $sign = $signGenerator->getResult();
        $param['sign'] = $sign;

        $request = XML::build($param);

        //设置Http使用的证书
        $options['sslcert_path']=$this->business->getClientCert();
        $options['sslkey_path']=$this->business->getClientKey();

        $http = new Http();

        //根据红包类型决定调用的API
        if($type == self::TYPE_CASH_LUCK_MONEY){
            $url = self::API_SEND_LUCK_MONEY;
        }else{
            $url = self::API_SEND_GROUP_LUCK_MONEY;
        }
        $response = $http->request($url, Http::POST, $request, $options);

        if(empty($response)) {
            throw new Exception('Send LuckMoney failed.');
        }

        $result = XML::parse($response);

        return $result;
    }

    /**
     * 查询红包信息
     *
     * @param string $mchBillNumber
     *
     * @return array
     */
    public function query($mchBillNumber)
    {
        if(empty($mchBillNumber)){
            throw new Exception('mch_id is required');
        }

        $param['mch_billno'] = $mchBillNumber;
        $param['nonce_str'] = uniqid('pre_');
        $param['mch_id'] = $this->business->mch_id;
        $param['appid'] = $this->business->appid;
        $param['bill_type'] = 'MCHT';

        $signGenerator = new SignGenerator($param);
        $signGenerator->onSortAfter(function(SignGenerator $that) {
            $that->key = $this->business->mch_key;
        });

        $sign = $signGenerator->getResult();
        $param['sign'] = $sign;

        $request = XML::build($param);

        //设置Http使用的证书
        $options['sslcert_path']=$this->business->getClientCert();
        $options['sslkey_path']=$this->business->getClientKey();

        $http = new Http();
        $response = $http->request(static::API_QUERY_LUCK_MONEY, Http::POST, $request, $options);

        if(empty($response)) {
            throw new Exception('Get LuckMoneyInfo failed.');
        }

        $result = XML::parse($response);

        return $result;
    }
}
