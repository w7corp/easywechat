<?php namespace Overtrue\Wechat\Utils;

use Exception;
use Crypt\PKCS7;

class Crypt {

    protected $appId;
    protected $AESKey;
    protected $token;

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

    public function _construct($appId, $AESKey, $token = '')
    {
        $this->appId  = $appId;
        $this->AESKey = $AESKey;
        $this->token  = $token;

        set_exception_handler(function($e){
            error_log($this->errors[$e->getCode()]);
            exit($e->getCode);
        });
    }

    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @param string  $reply        公众平台待回复用户的消息，xml格式的字符串
     * @param integer $timestamp    时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param string  $nonce        随机串，可以自己生成，也可以用URL参数的nonce
     *
     * @return string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp,
     *                nonce, encrypt的xml格式的字符串,当return返回0时有效
     */
    public function encryptMsg($reply, $nonce, $timestamp = null)
    {
        $encrypt = $this->encrypt($reply, $this->appId);

        $timestamp || $timestamp = time();

        //生成安全签名
        $signature = $this->getSHA1($this->token, $timestamp, $nonce, $encrypt);

        $response = array(
            'MsgSignature' => $signature,
            'TimeStamp'    => $timestamp,
            'Nonce'        => $nonce,
            'Encrypt'      => $encrypt,
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
     * @param string $msgSignature  签名串，对应URL参数的msg_signature
     * @param string $timestamp     时间戳 对应URL参数的timestamp
     * @param string $nonce         随机串，对应URL参数的nonce
     * @param string $postXML          密文，对应POST请求的数据
     * @param string &$msg          解密后的原文，当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    private function decryptMsg($msgSignature, $nonce, $postXML, $timestamp = null)
    {
        if (strlen($this->aesKey) != 43) {
            throw new Exception('Invalid AESKey.', self::ERROR_INVALID_AESKEY);
        }

        //提取密文
        $array = $this->extract($postXML);

        if (empty($array)) {
            throw new Exception('Invalid xml.', self::ERROR_PARSE_XML);
        }

        $timestamp || $timestamp = time();

        $encrypted  = $array['Encrypt'];
        $toUserName = $array['ToUserName'];

        //验证安全签名
        $signature = $this->getSHA1($this->token, $this->timestamp, $nonce, $encrypted);

        if ($signature != $msgSignature) {
            $this->expception('Invalid Signature.', self::ERROR_INVALID_SIGNATURE);
        }

        return $this->decrypt($encrypted, $this->appId);;
    }

    /**
     * 对明文进行加密
     *
     * @param string $text 需要加密的明文
     *
     * @return string 加密后的密文
     */
    private function encrypt($text, $appId)
    {
        try {
            //获得16位随机字符串，填充到明文之前
            $random = $this->getRandomStr();
            $text   = $random . pack("N", strlen($text)) . $text . $appId;

            // 网络字节序
            $size   = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv     = substr($this->AESKey, 0, 16);

            //使用自定义的填充方式对明文进行补位填充
            $text   = $this->encode($text);

            mcrypt_generic_init($module, $this->AESKey, $iv);

            //加密
            $encrypted = mcrypt_generic($module, $text);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);

            //使用BASE64对加密后的字符串进行编码
            return base64_encode($encrypted);

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), self::ERROR_ENCRYPT_AES);
        }
    }

    /**
     * 对密文进行解密
     *
     * @param string $encrypted 需要解密的密文
     *
     * @return string 解密得到的明文
     */
    private function decrypt($encrypted)
    {
        try {
            //使用BASE64对需要解密的字符串进行解码
            $ciphertext_dec = base64_decode($encrypted);
            $module         = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv             = substr($this->AESKey, 0, 16);

            mcrypt_generic_init($module, $this->AESKey, $iv);

            //解密
            $decrypted = mdecrypt_generic($module, $ciphertext_dec);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), self::ERROR_DECRYPT_AES);
        }

        try {
            //去除补位字符
            $result = $this->decode($decrypted);

            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16) {
                return "";
            }

            $content   = substr($result, 16, strlen($result));
            $listLen   = unpack("N", substr($content, 0, 4));
            $xmlLen    = $listLen[1];
            $xml       = substr($content, 4, $xmlLen);
            $fromAppId = substr($content, $xmlLen + 4);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), self::ERROR_INVALID_XML);
        }

        if ($fromAppId != $this->appId) {
            throw new Exception($e->getMessage(), self::ERROR_INVALID_APPID);
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
        $strSource = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strSource) - 1;

        $str = "";
        for ($i = 0; $i < 16; $i++) {
            $str .= $strSource[mt_rand(0, $max)];
        }

        return $str;
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
}