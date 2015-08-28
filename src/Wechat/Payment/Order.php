<?php
/**
 * Order.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Frye <frye0423@gmail.com>
 * @copyright 2015 Frye <frye0423@gmail.com>
 * @link      https://github.com/0i
 * @link      http://blog.lost-magic.com
 * @link      https://github.com/thenbsp/Wechat
 */

namespace Overtrue\Wechat\Payment;

use Overtrue\Wechat\Utils\Bag;
// use Overtrue\Wechat\Utils\Bag;

class Order
{   
    
    /**
     * 接口地址
     */
    public $url;

    /**
     * 参数包
     * @var String(32)
     */
    protected $bag;

    /**
     * 商户 key
     */
    protected $key;

    /**
     * 订单必填项
     * 
     * @var array
     */
    protected $required = array();

    /**
     * 订单选填项
     * 
     * @var array
     */
    protected $optional = array();

    /**
     * 商户证书 cert
     */
    protected $clientCert;
    
    /**
     * 商户证书 key
     */
    protected $clientKey;

    public function __construct(Bag $bag, $key)
    {
        $this->bag = $bag;
        $this->key = $key;
    }

    /**
     * 获取请求参数包
     * 
     * @return [array] 请求参数
     */
    public function getBag()
    {
        return $this->bag;
    }

    /**
     * 获取商户key
     * 
     * @return [string]
     */
    public function getKey(){
        return $this->key;
    }

    /**
     * 获取必选参数
     * 
     * @return [array] [必选参数数组]
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * 设置商户证书 cert
     * 
     * @param $filepath
     *
     * @return $this
     * @throws Exception
     */
    public function setClientCert($filepath)
    {
        if( !file_exists($filepath) ) {
            throw new Exception(sprintf('client_cert "%s" is not found', $filepath));
        }
        $this->clientCert = $filepath;
        return $this;
    }

    /**
     * 获取商户证书 cert
     * @return string
     */
    public function getClientCert()
    {
        return $this->clientCert;
    }

    /**
     * 设置商户证书 key
     * @param $filepath
     *
     * @return $this
     * @throws Exception
     */
    public function setClientKey($filepath)
    {
        if (!file_exists($filepath)) {
            throw new Exception(sprintf('client_key "%s" is not found', $filepath));
        }
        $this->clientKey = $filepath;
        return $this;
    }

    /**
     * 获取商户证书 key
     * @return string
     */
    public function getClientKey()
    {
        return $this->clientKey;
    }

    /**
     * 构造ssl请求参数
     * @return [array]
     */
    public function sslOption()
    {   
        if (!$this->clientCert || !$this->clientKey) {
            $option = array();
        }else{
            $option = array(
                'ssl' => array(
                            'cert'  => $this->clientCert, 
                            'key'   => $this->clientKey
                        )
            );
        }
        return $option;
    }
}