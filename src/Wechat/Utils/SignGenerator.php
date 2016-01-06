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
 * Util.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Frye <frye0423@gmail.com>
 * @copyright 2015 Frye <frye0423@gmail.com>
 *
 * @link      https://github.com/0i
 * @link      http://blog.lost-magic.com
 * @link      https://github.com/thenbsp/Wechat
 */

namespace Overtrue\Wechat\Utils;

/**
 * 签名生成器（专门用于生成微信各种签名）
 * Created by thenbsp (thenbsp@gmail.com)
 * Created at 2015/08/06.
 */
class SignGenerator extends MagicAttributes
{
    /**
     * 加密类型.
     */
    protected $hashType = 'md5';

    /**
     * 是否转为大写.
     */
    protected $isUpper = true;

    /**
     * 排序回调函数.
     */
    protected $sortAfterCallback;

    public function __construct(array $params = array())
    {
        $this->attributes = $params;
    }

    /**
     * 移除一项.
     *
     * @param $key
     *
     * @return $this
     */
    public function removeParams($key)
    {
        unset($this->attributes[$key]);

        return $this;
    }

    /**
     * 设置加密类型.
     *
     * @param $hashType
     *
     * @throws \Exception
     */
    public function setHashType($hashType)
    {
        $type = strtolower($hashType);
        if (!in_array($type, array('md5', 'sha1'), true)) {
            throw new \Exception(sprintf('Invalid Hash Type %s', $hashType));
        }
        $this->hashType = $type;
    }

    /**
     * 是否转为大写.
     *
     * @param $value
     *
     * @return bool
     */
    public function setUpper($value)
    {
        return $this->isUpper = (bool) $value;
    }

    /**
     * 将全部项目排序.
     */
    public function sortable()
    {
        ksort($this->attributes);
        if (is_callable($this->sortAfterCallback)) {
            call_user_func($this->sortAfterCallback, $this);
        }
    }

    /**
     * 排序之后调用（事件）.
     *
     * @param callable $callback
     */
    public function onSortAfter($callback)
    {
        $this->sortAfterCallback = $callback;
    }

    /**
     * 获取签结果.
     *
     * @return string
     */
    public function getResult()
    {
        $this->sortable();
        $query = http_build_query($this->attributes);
        $query = urldecode($query);
        $result = call_user_func($this->hashType, $query);

        return $this->isUpper ? strtoupper($result) : $result;
    }
}
