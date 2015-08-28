<?php
/**
 * Payment.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    peiwen <haopeiwen123@gmail.com>
 * @copyright 2015 peiwen <haopeiwen123@gmail.com>
 * @link      https://github.com/troubleman
 * @link      https://github.com/troubleman/Wechat
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Http;
use Overtrue\Wechat\Util\Bag;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Utils\JSON;
use Overtrue\Wechat\Utils\SignGenerator;
use Overtrue\Wechat\Utils\Arr;
use Overtrue\Wechat\Payment\Order;

/**
 * 微信支付相关接口调用统一入口
 */
class Payment
{
    /**
     * 接口请求参数
     * 
     * @var Order
     */
    protected $order;

    /**
     * 参数包
     */
    protected $bag;

    /**
     * 商户key
     */
    protected $key;

    /**
     * 请求结果
     *
     * @var Array
     */
    protected $response;
    
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->bag = $order->getBag();
        $this->key = $order->getKey();
    }

    //检查必填参数
    public function checkParams()
    {   
        $required = $this->order->getRequired();

        // 检测必填字段
        foreach($required as $paramName) {
            if (!$this->bag->has($paramName)) {
                throw new Exception(sprintf('"%s" is required', $paramName));
            }
        }
    }

    /**
     * 获取签名
     * @param  [array] $params 参与构造签名的所有请求参数
     * @param  [string] $key    商户的key
     * 
     * @return [string]         [description]
     */
    public function getSign(Array $params = null, $key = null){

        $params = $params ? : $this->bag->all();

        $key = $key ? : $this->key;

        $signGenerator = new SignGenerator($params);

        $signGenerator->onSortAfter(function(SignGenerator $that) use ($key) {
            $that->key = $key;
        });

        return $signGenerator->getResult();
    }

    /**
     * 发起一个请求
     * @return array 响应结果
     */
    public function getResponse()
    {
        $this->checkParams();

        $params = $this->bag->all();

        $params['sign'] = $this->getSign($params, $this->key);

        $request = XML::build($params);
        
        $http = new Http();

        $option = $this->order->sslOption();

        $response = $http->request($this->order->url, Http::POST, $request, $option);

        $response = XML::parse($response);

        if (isset($response['result_code']) &&
            ($response['result_code'] === 'FAIL')) {
            throw new Exception($response['err_code'].': '.$response['err_code_des']);
        }
        if (isset($response['return_code']) &&
            $response['return_code'] === 'FAIL') {
            throw new Exception($response['return_code'].': '.$response['return_msg']);
        }

        return $this->response = $response;
    }

    /**
     * 获取配置文件（用于 WeixinJSBridge invoke 方式）
     * 
     * @param bool|true $asJson
     *
     * @return array|string
     */
    public function getConfig($prepay_id, $asJson = true)
    {   
        $params = array(
            'appId'     => $this->bag->appid,
            'timeStamp' => (string) time(),
            'nonceStr'  =>  $this->bag->nonce_str,
            'package'   => 'prepay_id='.$prepay_id,
            'signType'  => 'MD5'
        );
        $params['paySign'] = $this->getSign($params, $this->key);

        Arr::forget($params, 'signType');

        return $asJson ? JSON::encode($params) : $params;
    }

}
