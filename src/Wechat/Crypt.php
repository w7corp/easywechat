<?php
/**
 * Crypt.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me> AC <alexever@gmail.com>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\XML;

/**
 * 加密解密
 */
class Crypt
{

    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用token
     *
     * @var string
     */
    protected $token;

    /**
     * 加密用的AESkey
     *
     * @var string
     */
    protected $AESKey;

    /**
     * 块大小
     *
     * @var int
     */
    protected $blockSize;

    const ERROR_INVALID_SIGNATURE = -40001; // 校验签名失败
    const ERROR_PARSE_XML         = -40002; // 解析xml失败
    const ERROR_CALC_SIGNATURE    = -40003; // 计算签名失败
    const ERROR_INVALID_AESKEY    = -40004; // 不合法的AESKey
    const ERROR_INVALID_APPID     = -40005; // 校验AppID失败
    const ERROR_ENCRYPT_AES       = -40006; // AES加密失败
    const ERROR_DECRYPT_AES       = -40007; // AES解密失败
    const ERROR_INVALID_XML       = -40008; // 公众平台发送的xml不合法
    const ERROR_BASE64_ENCODE     = -40009; // Base64编码失败
    const ERROR_BASE64_DECODE     = -40010; // Base64解码失败
    const ERROR_XML_BUILD         = -40011; // 公众帐号生成回包xml失败

    /**
     * constructor
     *
     * @param string $appId
     * @param string $token
     * @param string $encodingAESKey
     */
    public function __construct($appId, $token, $encodingAESKey)
    {
        if (!extension_loaded('openssl')) {
            throw new Exception("The ext 'openssl' is required.");
        }

        $this->appId     = $appId;
        $this->token     = $token;
        $this->AESKey    = $encodingAESKey;
        $this->blockSize = 32;
    }

    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @param string $xml       公众平台待回复用户的消息，xml格式的字符串
     * @param string $nonce     随机串，可以自己生成，也可以用URL参数的nonce
     * @param int    $timestamp 时间戳，可以自己生成，也可以用URL参数的timestamp
     *
     * @return string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp,
     *                nonce, encrypt的xml格式的字符串
     */
    public function encryptMsg($xml, $nonce = null, $timestamp = null)
    {
        $encrypt = $this->encrypt($xml, $this->appId);

        !is_null($nonce) || $nonce = substr($this->appId, 0, 10);
        !is_null($timestamp) || $timestamp = time();

        //生成安全签名
        $signature = $this->getSHA1($this->token, $timestamp, $nonce, $encrypt);

        $response = array(
                     'Encrypt'      => $encrypt,
                     'MsgSignature' => $signature,
                     'TimeStamp'    => $timestamp,
                     'Nonce'        => $nonce,
                    );

        //生成响应xml
        return XML::build($response);
    }

    /**
     * 检验消息的真实性，并且获取解密后的明文.
     * <ol>
     *    <li>利用收到的密文生成安全签名，进行签名验证</li>
     *    <li>若验证通过，则提取xml中的加密消息</li>
     *    <li>对消息进行解密</li>
     * </ol>
     *
     * @param string $msgSignature 签名串，对应URL参数的msg_signature
     * @param string $nonce        随机串，对应URL参数的nonce
     * @param string $timestamp    时间戳 对应URL参数的timestamp
     * @param string $postXML      密文，对应POST请求的数据
     *
     * @return array
     */
    public function decryptMsg($msgSignature, $nonce, $timestamp, $postXML)
    {
        //提取密文
        $array = XML::parse($postXML);

        if (empty($array)) {
            throw new Exception('Invalid xml.', self::ERROR_PARSE_XML);
        }

        $encrypted  = $array['Encrypt'];

        //验证安全签名
        $signature = $this->getSHA1($this->token, $timestamp, $nonce, $encrypted);

        if ($signature !== $msgSignature) {
            throw new Exception('Invalid Signature.', self::ERROR_INVALID_SIGNATURE);
        }

        return XML::parse($this->decrypt($encrypted, $this->appId));
    }

    /**
     * 对明文进行加密
     *
     * @param string $text  需要加密的明文
     * @param string $appId app id
     *
     * @return string 加密后的密文
     */
    private function encrypt($text, $appId)
    {
        try {
            $key = $this->getAESKey();
            $random = $this->getRandomStr();
            $text = $this->encode($random.pack('N', strlen($text)).$text.$appId);

            $iv = substr($key, 0, 16);

            $encrypted = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $iv);

            return base64_encode($encrypted);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), self::ERROR_ENCRYPT_AES);
        }
    }

    /**
     * 对密文进行解密
     *
     * @param string $encrypted 需要解密的密文
     * @param string $appId     app id
     *
     * @return string 解密得到的明文
     */
    private function decrypt($encrypted, $appId)
    {
        try {
            $key = $this->getAESKey();
            $ciphertext = base64_decode($encrypted, true);
            $iv = substr($key, 0, 16);

            $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $iv);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), self::ERROR_DECRYPT_AES);
        }

        try {
            //去除补位字符
            $result = $this->decode($decrypted);

            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16) {
                return '';
            }

            $content   = substr($result, 16, strlen($result));
            $listLen   = unpack('N', substr($content, 0, 4));
            $xmlLen    = $listLen[1];
            $xml       = substr($content, 4, $xmlLen);
            $fromAppId = trim(substr($content, $xmlLen + 4));
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), self::ERROR_INVALID_XML);
        }

        if ($fromAppId !== $appId) {
            throw new Exception('Invalid appId.', self::ERROR_INVALID_APPID);
        }

        return $xml;
    }

    /**
     * 随机生成16位字符串
     *
     * @return string 生成的字符串
     */
    private function getRandomStr()
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'), 0, 16);
    }

    /**
     * Return AESKey.
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getAESKey()
    {
        if (empty($this->AESKey) || strlen($this->AESKey) !== 43) {
            throw new Exception("Configuration mission, 'aes_key' is required.");
        }

        return base64_decode($this->AESKey.'=', true);
    }

    /**
     * 生成SHA1签名
     *
     * @return string
     */
    public function getSHA1()
    {
        try {
            $array = func_get_args();
            sort($array, SORT_STRING);

            return sha1(implode($array));
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), self::ERROR_CALC_SIGNATURE);
        }
    }

    /**
     * 对需要加密的明文进行填充补位
     *
     * @param string $text 需要进行填充补位操作的明文
     *
     * @return string 补齐明文字符串
     */
    public function encode($text)
    {
        //计算需要填充的位数
        $padAmount = $this->blockSize - (strlen($text) % $this->blockSize);

        $padAmount = $padAmount !== 0 ? $padAmount : $this->blockSize;

        //获得补位所用的字符
        $padChr = chr($padAmount);

        $tmp = '';

        for ($index = 0; $index < $padAmount; $index++) {
            $tmp .= $padChr;
        }

        return $text.$tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     *
     * @param string $decrypted 解密后的明文
     *
     * @return string 删除填充补位后的明文
     */
    public function decode($decrypted)
    {
        $pad = ord(substr($decrypted, -1));

        if ($pad < 1 || $pad > $this->blockSize) {
            $pad = 0;
        }

        return substr($decrypted, 0, (strlen($decrypted) - $pad));
    }
}
