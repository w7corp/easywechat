<?php
/**
 * Wechat.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

use ReflectionClass;

/**
 * SDK 入口
 */
class Wechat
{
    /**
     * 配置信息
     *
     * <pre>
     * [
     *   'use_alias'    => false,
     *   'app_id'       => 'YourAppId', // 必填
     *   'secret'       => 'YourSecret', // 必填
     *   'token'        => 'YourToken',  // 必填
     *   'encoding_key' => 'YourEncodingAESKey' // 加密模式需要，其它模式不需要
     * ]
     * </pre>
     *
     * @var array
     */
    protected static $config;

    /**
     * 已经实例化的对象
     *
     * @var array
     */
    protected static $resolved;

    /**
     * 初始化配置
     *
     * @param array $config 配置项
     *
     * @return void
     */
    public static function config(array $config)
    {
        self::$config = $config;
    }

    /**
     * 获取服务
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public static function service($name, $args = array())
    {
        return $this->build("Overtrue\\Wechat\\" . self::camelCase($name), $args);
    }

    /**
     * 获取消息
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public static function message($name, $args = array())
    {
        return $this->build("Overtrue\\Wechat\\Messages\\" . self::camelCase($name), $args);
    }

    /**
     * 获取工具对象
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public static function util($name, $args = array())
    {
        return $this->build("Overtrue\\Wechat\\Utils\\" . self::camelCase($name), $args);
    }

    /**
     * 获取对象实例
     *
     * @param string $class
     * @param array  $args
     *
     * @return mixed
     */
    public static function build($class, $args = array())
    {
        //TODO
        $args = array_merge(self::$config, $args);

        if ($instance = self::getResolved($class, $args)) {
            return $instance;
        }

        $reflectedClass = new ReflectionClass($class);

        return $reflectedClass->newInstanceArgs($args);
    }

    /**
     * 字符串转驼峰
     *
     * @param string $string 字符串
     *
     * @return string
     */
    public static function camelCase($string)
    {
        return preg_replace_callback(
               '/_{1,}([a-z])/',
               function($pipe){
                   return strtolower($pipe[1]);
               },
              $string);
    }

    /**
     * 获取已经实例化的对象
     *
     * @param string $class 类名
     * @param array  $args  参数
     *
     * @return mixed
     */
    protected static function getResolved($class, $args)
    {
        $key = $class.json_encode($args);

        return isset(self::$resolved[$key]) ? self::$resolved[$key] : null;
    }
}