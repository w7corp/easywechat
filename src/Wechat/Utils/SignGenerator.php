<?php
/**
 * Util.php
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

namespace Overtrue\Wechat\Utils;

/**
 * 签名生成器（专门用于生成微信各种签名）
 * Created by thenbsp (thenbsp@gmail.com)
 * Created at 2015/08/06
 */
class SignGenerator
{
    /**
     * 参与签名的 Key=>Value
     */
    protected $params = array();
    
    /**
     * 加密类型
     */
    protected $hashType = 'md5';
    
    /**
     * 是否转为大写
     */
    protected $isUpper = true;
    
    /**
     * 排序回调函数
     */
    protected $sortAfterCallback;
    
    public function __construct(array $params = array())
    {
        $this->params = $params;
    }

    /**
     * 检测是否包含某项
     * @param $key
     *
     * @return bool
     */
    public function hasParams($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * 获取参数
     * @param null $key
     * @param null $default
     *
     * @return array|null
     */
    public function getParams($key = null, $default = null)
    {
        if( !is_null($key) ) {
            return $this->hasParams($key) ?
                $this->params[$key] : $default;
        }
        return $this->params;
    }

    /**
     * 添加一项（重复添加前者会被覆盖）
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function addParams($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * 移除一项
     * @param $key
     *
     * @return $this
     */
    public function removeParams($key)
    {
        if( $this->hasParams($key) ) {
            unset($this->params[$key]);
        }
        return $this;
    }

    /**
     * 设置加密类型
     * @param $hashType
     *
     * @throws \Exception
     */
    public function setHashType($hashType)
    {
        $type = strtolower($hashType);
        if( !in_array($type, array('md5', 'sha1'), true) ) {
            throw new \Exception(sprintf('Invalid Hash Type %s', $hashType));
        }
        $this->hashType = $type;
    }

    /**
     * 是否转为大写
     * @param $value
     *
     * @return bool
     */
    public function setUpper($value)
    {
        return $this->isUpper = (bool) $value;
    }
    
    /**
     * 将全部项目排序
     */
    public function sortable()
    {
        ksort($this->params);
        if( is_callable($this->sortAfterCallback) ) {
            call_user_func($this->sortAfterCallback, $this);
        }
    }

    /**
     * 排序之后调用（事件）
     * @param callable $callback
     */
    public function onSortAfter(callable $callback)
    {
        $this->sortAfterCallback = $callback;
    }

    /**
     * 获取签结果
     * @return mixed|string
     */
    public function getResult()
    {
        $this->sortable();
        $query = http_build_query($this->params);
        $query = urldecode($query);
        $result = call_user_func($this->hashType, $query);
        return $this->isUpper ? strtoupper($result) : $result;
    }
}