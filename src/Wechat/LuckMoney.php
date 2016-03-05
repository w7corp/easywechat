<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * LuckMoney.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author bontian <bontian@163.com>
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Payment\Business;
use Overtrue\Wechat\Utils\SignGenerator;
use Overtrue\Wechat\Utils\XML;

class LuckMoney
{
    /**
     * 发送现金红包.
     */
    const API_SEND = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';

    /**
     * 发送裂变红包.
     */
    const API_GROUP_SEND = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack';

    /**
     * 红包查询.
     */
    const API_QUERY = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo';

    /**
     * 红包预下单接口.
     */
    const API_PREORDER = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/hbpreorder';

    const TYPE_CASH_LUCK_MONEY = 'NORMAL';   //红包类型，现金红包
    const TYPE_GROUP_LUCK_MONEY = 'GROUP';  //红包类型，裂变红包

    /**
     * 商户信息.
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
     * 设置商户.
     *
     * @param Business $business
     *
     * @return $this
     */
    public function setBusiness(Business $business)
    {
        if (!is_null($business)) {
            $this->business = $business;
        }

        return $this;
    }

    /**
     * 红包预下单，主要用于摇一摇红包活动.
     *
     * <pre>
     * $data:
     * {
     *     "mch_billno": "198374613512",
     *     "send_name":"某某公司",
     *     "hb_type":"某某公司",
     *     "total_amount": 1000,
     *     "total_num": 1,
     *     "wishing": "祝福语",
     *     "act_name": "活动名称",
     *     "remark": "红包备注"，
     *     "risk_cntl": "NORMAL"，
     * }
     * </pre>
     *
     * @param array $data
     *
     * @return array
     */
    public function preOrder(array $data)
    {
        $defaultParam['nonce_str'] = uniqid('pre_');
        $defaultParam['mch_id'] = $this->business->mch_id;
        $defaultParam['wxappid'] = $this->business->appid;

        //用于发红包时微信支付识别摇周边红包，所有开发者统一填写摇周边平台的商户号：1000052601
        $defaultParam['auth_mchid'] = '1000052601';
        //用于发红包时微信支付识别摇周边红包，所有开发者统一填写摇周边平台的appid:wxbf42bd79c4391863
        $defaultParam['auth_appid'] = 'wxbf42bd79c4391863';

        $defaultParam['amt_type'] = 'ALL_RAND';

        $param = array_merge($data, $defaultParam);
        $signGenerator = new SignGenerator($param);
        $me = $this;
        $signGenerator->onSortAfter(function (SignGenerator $that) use ($me) {
            $that->key = $me->business->mch_key;
        });

        $sign = $signGenerator->getResult();
        $param['sign'] = $sign;

        $request = XML::build($param);

        //设置Http使用的证书
        $options['sslcert_path'] = $this->business->getClientCert();
        $options['sslkey_path'] = $this->business->getClientKey();

        $http = new Http();

        $response = $http->request(self::API_PREORDER, Http::POST, $request, $options);

        if (empty($response)) {
            throw new Exception('Create PreOrder failed.');
        }

        $result = XML::parse($response);

        return $result;
    }

    /**
     * 发送红包.
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
     * @param int   $type
     *
     * @return array
     */
    public function send(array $data, $type = self::TYPE_CASH_LUCK_MONEY)
    {
        $defaultParam['nonce_str'] = uniqid('pre_');
        $defaultParam['mch_id'] = $this->business->mch_id;
        $defaultParam['wxappid'] = $this->business->appid;

        if ($type == self::TYPE_CASH_LUCK_MONEY) {
            $defaultParam['client_ip'] = $_SERVER['REMOTE_ADDR'];
        }

        if ($type == self::TYPE_GROUP_LUCK_MONEY) {
            $defaultParam['amt_type'] = 'ALL_RAND';
        }

        $param = array_merge($data, $defaultParam);
        $signGenerator = new SignGenerator($param);
        $me = $this;
        $signGenerator->onSortAfter(function (SignGenerator $that) use ($me) {
            $that->key = $me->business->mch_key;
        });

        $sign = $signGenerator->getResult();
        $param['sign'] = $sign;

        $request = XML::build($param);

        //设置Http使用的证书
        $options['sslcert_path'] = $this->business->getClientCert();
        $options['sslkey_path'] = $this->business->getClientKey();

        $http = new Http();

        //根据红包类型决定调用的API
        if ($type == self::TYPE_CASH_LUCK_MONEY) {
            $url = self::API_SEND;
        } else {
            $url = self::API_GROUP_SEND;
        }
        $response = $http->request($url, Http::POST, $request, $options);

        if (empty($response)) {
            throw new Exception('Send LuckMoney failed.');
        }

        $result = XML::parse($response);

        return $result;
    }

    /**
     * 发送普通红包.
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
     *
     * @return array
     */
    public function sendNormal(array $data)
    {
        return $this->send($data, self::TYPE_CASH_LUCK_MONEY);
    }

    /**
     * 发送裂变红包.
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
     *
     * @return array
     */
    public function sendGroup(array $data)
    {
        return $this->send($data, self::TYPE_GROUP_LUCK_MONEY);
    }

    /**
     * 查询红包信息.
     *
     * @param string $mchBillNumber
     *
     * @return array
     */
    public function query($mchBillNumber)
    {
        if (empty($mchBillNumber)) {
            throw new Exception('mch_id is required');
        }

        $param['mch_billno'] = $mchBillNumber;
        $param['nonce_str'] = uniqid('pre_');
        $param['mch_id'] = $this->business->mch_id;
        $param['appid'] = $this->business->appid;
        $param['bill_type'] = 'MCHT';

        $signGenerator = new SignGenerator($param);
        $me = $this;
        $signGenerator->onSortAfter(function (SignGenerator $that) use ($me) {
            $that->key = $me->business->mch_key;
        });

        $sign = $signGenerator->getResult();
        $param['sign'] = $sign;

        $request = XML::build($param);

        //设置Http使用的证书
        $options['sslcert_path'] = $this->business->getClientCert();
        $options['sslkey_path'] = $this->business->getClientKey();

        $http = new Http();
        $response = $http->request(static::API_QUERY, Http::POST, $request, $options);

        if (empty($response)) {
            throw new Exception('Get LuckMoneyInfo failed.');
        }

        $result = XML::parse($response);

        return $result;
    }
}
