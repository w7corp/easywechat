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


class Order
{
    /**
     * 订单参数
     */
    protected $params;
    
    /**
     * 订单必填项
     */
    protected $required = array('body', 'out_trade_no', 'total_fee', 'notify_url');
    
    /**
     * 订单选填项
     */
    protected $optional = array(
        'device_info', 'detail', 'attach', 'fee_type', 'time_start', 'time_expire',
        'goods_tag', 'product_id', 'limit_pay', 'nonce_str', 'spbill_create_ip',
        'trade_type', 'openid'
    );
    
    public function __construct() { }

    /**
     * 获取参数
     * @param string $paramName
     * @param string $default
     *
     * @return string
     */
    public function getParams($paramName = null, $default = null)
    {
        if( null !== $paramName ) {
            return $this->hasParams($paramName) ?
                $this->params[$paramName] : $default;
        }
        return $this->params;
    }

    /**
     * 检测是否包含指定参数
     * @param $paramName
     *
     * @return bool
     */
    public function hasParams($paramName)
    {
        return isset($this->params[$paramName]);
    }

    /**
     * 检测参数值是否有效
     * @throws Exception
     */
    public function checkParams()
    {
        foreach($this->required AS $paramName) {
            if( !$this->hasParams($paramName) ) {
                throw new Exception(sprintf('"%s" is required', $paramName));
            }
        }
    }
    
    public function __call($method, $arguments)
    {
        if( !count($arguments) ) {
            throw new Exception(sprintf('Missing argument 1 for %s::%s()',
                __CLASS__, $method
            ));
        }
        if( !in_array($method, array_merge($this->required, $this->optional)) ) {
            throw new Exception(sprintf('Invalid argument "%s"', $method));
        }
        $this->params[$method] = (string) $arguments[0];
        return $this;
    }
}