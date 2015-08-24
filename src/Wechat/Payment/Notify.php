<?php
/**
 * Notify.php
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
 * @link      https://github.com/0i/Wechat
 */

namespace Overtrue\Wechat\Payment;

use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Utils\SignGenerator;

class Notify
{
    protected $appId;
    protected $appSecret;
    protected $mchId;
    protected $mchKey;
    
    /**
     * @var Bag
     */
    protected $transaction;

    public function __construct($appId, $appSecret, $mchId, $mchKey) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->mchId = $mchId;
        $this->mchKey = $mchKey;
    }

    /**
     * 验证订单消息是否合法,
     * 不合法返回false, 合法返回订单信息详情
     * 
     * @return bool|Bag
     */
    public function verify() {
        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            if (!empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
                $xmlInput = $GLOBALS['HTTP_RAW_POST_DATA'];
            } else {
                $xmlInput = file_get_contents('php://input');
            }
        } else {
            $xmlInput = file_get_contents('php://input');
        }

        if (empty($xmlInput)) {
            return false;
        }

        $input = XML::parse($xmlInput);
        if (empty($input) || empty($input['sign'])) {
            return false;
        }
        
        $sign = $input['sign'];
        unset($input['sign']);
        $signGenerator = new SignGenerator($input);
        $signGenerator->onSortAfter(function(SignGenerator $that) {
            $that->key = $this->mchKey;
        });
        if ($sign !== $signGenerator->getResult()) {
            return false;
        }
        
        return $this->transaction = new Bag($input);
    }

    /**
     * 回复消息, 如果不回复, 微信会一直发送请求到notify_url
     * 
     * @param string $code
     * @param string $msg
     *
     * @return string
     */
    public function reply($code = 'SUCCESS', $msg = 'OK') {
        $params = [
            'return_code' => $code,
            'return_msg' => $msg,
        ];
        
        return XML::build($params);
    }
    
}