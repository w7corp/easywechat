<?php

/**
 * Cryptor.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Encryption;

use EasyWeChat\Core\Exceptions\InvalidConfigException;
use EasyWeChat\Core\Exceptions\RuntimeException;
use EasyWeChat\Support\XML;
use Exception as BaseException;

/**
 * Class Cryptor.
 */
class Cryptor
{
    /**
     * App id.
     *
     * @var string
     */
    protected $appId;

    /**
     * App token.
     *
     * @var string
     */
    protected $token;

    /**
     * AES key.
     *
     * @var string
     */
    protected $AESKey;

    /**
     * Block size.
     *
     * @var int
     */
    protected $blockSize;

    /**
     * Constructor.
     *
     * @param string $appId
     * @param string $token
     * @param string $AESKey
     *
     * @throws RuntimeException
     */
    public function __construct($appId, $token, $AESKey)
    {
        if (!extension_loaded('mcrypt')) {
            throw new RuntimeException("The ext 'mcrypt' is required.");
        }

        $this->appId = $appId;
        $this->token = $token;
        $this->AESKey = $AESKey;
        $this->blockSize = 32;// mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    }

    /**
     * Encrypt the message and return XML.
     *
     * @param string $xml
     * @param string $nonce
     * @param int    $timestamp
     *
     * @return string
     */
    public function encryptMsg($xml, $nonce = null, $timestamp = null)
    {
        $encrypt = $this->encrypt($xml, $this->appId);

        !is_null($nonce) || $nonce = substr($this->appId, 0, 10);
        !is_null($timestamp) || $timestamp = time();

        //生成安全签名
        $signature = $this->getSHA1($this->token, $timestamp, $nonce, $encrypt);

        $response = [
            'Encrypt' => $encrypt,
            'MsgSignature' => $signature,
            'TimeStamp' => $timestamp,
            'Nonce' => $nonce,
        ];

        //生成响应xml
        return XML::build($response);
    }

    /**
     * Decrypt message.
     *
     * @param string $msgSignature
     * @param string $nonce
     * @param string $timestamp
     * @param string $postXML
     *
     * @return array
     *
     * @throws Exception
     */
    public function decryptMsg($msgSignature, $nonce, $timestamp, $postXML)
    {
        try {
            $array = XML::parse($postXML);
        } catch (BaseException $e) {
            throw new Exception('Invalid xml.', Exception::ERROR_PARSE_XML);
        }

        $encrypted = $array['Encrypt'];

        $signature = $this->getSHA1($this->token, $timestamp, $nonce, $encrypted);

        if ($signature !== $msgSignature) {
            throw new Exception('Invalid Signature.', Exception::ERROR_INVALID_SIGNATURE);
        }

        return XML::parse($this->decrypt($encrypted, $this->appId));
    }

    /**
     * Get SHA1.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getSHA1()
    {
        try {
            $array = func_get_args();
            sort($array, SORT_STRING);

            return sha1(implode($array));
        } catch (BaseException $e) {
            throw new Exception($e->getMessage(), Exception::ERROR_CALC_SIGNATURE);
        }
    }

    /**
     * Encode string.
     *
     * @param string $text
     *
     * @return string
     */
    public function encode($text)
    {
        $padAmount = $this->blockSize - (strlen($text) % $this->blockSize);

        $padAmount = $padAmount !== 0 ? $padAmount : $this->blockSize;

        $padChr = chr($padAmount);

        $tmp = '';

        for ($index = 0; $index < $padAmount; ++$index) {
            $tmp .= $padChr;
        }

        return $text.$tmp;
    }

    /**
     * Decode string.
     *
     * @param string $decrypted
     *
     * @return string
     */
    public function decode($decrypted)
    {
        $pad = ord(substr($decrypted, -1));

        if ($pad < 1 || $pad > $this->blockSize) {
            $pad = 0;
        }

        return substr($decrypted, 0, (strlen($decrypted) - $pad));
    }

    /**
     * Encrypt string.
     *
     * @param string $text
     * @param string $appId
     *
     * @return string
     *
     * @throws Exception
     */
    private function encrypt($text, $appId)
    {
        try {
            $key = $this->getAESKey();
            $random = $this->getRandomStr();
            $text = $random.pack('N', strlen($text)).$text.$appId;

            // $size   = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($key, 0, 16);

            $text = $this->encode($text);

            mcrypt_generic_init($module, $key, $iv);

            $encrypted = mcrypt_generic($module, $text);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);

            return base64_encode($encrypted);
        } catch (BaseException $e) {
            throw new Exception($e->getMessage(), Exception::ERROR_ENCRYPT_AES);
        }
    }

    /**
     * Decrypt message.
     *
     * @param string $encrypted
     * @param string $appId
     *
     * @return string
     *
     * @throws Exception
     */
    private function decrypt($encrypted, $appId)
    {
        try {
            $key = $this->getAESKey();
            $ciphertext = base64_decode($encrypted, true);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($key, 0, 16);

            mcrypt_generic_init($module, $key, $iv);

            $decrypted = mdecrypt_generic($module, $ciphertext);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (BaseException $e) {
            throw new Exception($e->getMessage(), Exception::ERROR_DECRYPT_AES);
        }

        try {
            $result = $this->decode($decrypted);

            if (strlen($result) < 16) {
                return '';
            }

            $content = substr($result, 16, strlen($result));
            $listLen = unpack('N', substr($content, 0, 4));
            $xmlLen = $listLen[1];
            $xml = substr($content, 4, $xmlLen);
            $fromAppId = trim(substr($content, $xmlLen + 4));
        } catch (BaseException $e) {
            throw new Exception($e->getMessage(), Exception::ERROR_INVALID_XML);
        }

        if ($fromAppId !== $appId) {
            throw new Exception('Invalid appId.', Exception::ERROR_INVALID_APPID);
        }

        return $xml;
    }

    /**
     * Return AESKey.
     *
     * @return string
     *
     * @throws InvalidConfigException
     */
    protected function getAESKey()
    {
        static $key;

        if ($key) {
            return $key;
        }

        if (empty($this->AESKey) || strlen($this->AESKey) !== 43) {
            throw new InvalidConfigException("Configuration mission, 'aes_key' is required.");
        }

        return $key = base64_decode($this->AESKey.'=', true);
    }
}//end class

