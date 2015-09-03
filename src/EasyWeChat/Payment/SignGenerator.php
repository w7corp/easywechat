<?php
/**
 * SignGenerator.php
 *
 * Part of Weibo\MAPI\Authentication.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    zhengchao3 <zhengchao3@staff.weibo.com>
 * @copyright 2015 weibo.com
 *
 * @link      https://gitlab.weibo.cn/mobile-api/weibo-authentication
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Payment;

/**
 * Class SignGenerator.
 */
class SignGenerator
{
    /**
     * Secret key.
     *
     * @var string
     */
    protected $key;

    /**
     * The method to encrypt.
     *
     * @var string
     */
    protected $encryptMethod;

    /**
     * Constructor.
     *
     * @param string $key
     * @param string $encryptMethod
     */
    public function __construct($key, $encryptMethod = 'md5')
    {
        $this->key = $key;
        $this->encryptMethod = $encryptMethod;
    }

    /**
     * Generate a signature.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function generate($attributes)
    {
        ksort($attributes);

        $attributes['key'] = $this->key;

        $string = http_build_query($attributes);

        $string = call_user_func_array($this->encryptMethod, [$string]);

        return strtoupper($string);
    }
}