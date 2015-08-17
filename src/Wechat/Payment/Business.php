<?php
/**
 * Business.php
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

class Business
{
    /**
     * 商户基本信息
     */
    protected $params;
    
    /**
     * 有效的参数
     */
    protected $valids = array('appid', 'appsecret', 'mch_id', 'mch_key');
    
    /**
     * 商户证书 cert
     */
    protected $clientCert;
    
    /**
     * 商户证书 key
     */
    protected $clientKey;

    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $mchId
     * @param string $mchKey
     */
    public function __construct($appId, $appSecret, $mchId, $mchKey) {
        $this->appid($appId);
        $this->appsecret($appSecret);
        $this->mch_id($mchId);
        $this->mch_key($mchKey);
    }

    /**
     * @param string $paramName
     * @param string $default
     *
     * @return string
     */
    public function getParams($paramName = null, $default = null)
    {
        if(null !== $paramName) {
            return $this->hasParams($paramName) ? $this->params[$paramName] : $default;
        }
        return $this->params;
    }

    /**
     * @param $paramName
     *
     * @return bool
     */
    public function hasParams($paramName)
    {
        return isset($this->params[$paramName]);
    }

    /**
     * 设置商户证书 cert
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
        if( !file_exists($filepath) ) {
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
     * 检测参数值是否有效
     */
    public function checkParams()
    {
        foreach($this->valids AS $paramName) {
            if( !$this->hasParams($paramName) ) {
                throw new Exception(sprintf('"%s" is required', $paramName));
            }
        }
    }
    
    public function __call($method, $arguments)
    {
        if( !in_array($method, $this->valids) ) {
            throw new Exception(sprintf('Invalid arguments "%s"', $method));
        }
        $this->params[$method] = $arguments[0];
        return $this;
    }
}