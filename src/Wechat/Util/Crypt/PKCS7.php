<?php namespace Overtrue\Wechat\Util\Crypt;

class PKCS7 {

    static protected $blockSize = 32;

    /**
     * 对需要加密的明文进行填充补位
     *
     * @param string  $text      需要进行填充补位操作的明文
     *
     * @return string 补齐明文字符串
     */
    static public function encode($text)
    {
        //计算需要填充的位数
        $padAmount = self::$blockSize - (mb_strlen($text) % self::$blockSize);

        $padAmount = $padAmount ? $padAmount : self::$blockSize;

        //获得补位所用的字符
        $padChr = chr($padAmount);

        $tmp = "";

        for ($index = 0; $index < $padAmount; $index++) {
            $tmp .= $padChr;
        }

        return $text . $tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     *
     * @param string $decrypted 解密后的明文
     *
     * @return string 删除填充补位后的明文
     */
    static public decode($decrypted)
    {
        $pad = ord(substr($text, -1));

        if ($pad < 1 || $pad > self::$blockSize) {
            $pad = 0;
        }

        return substr($text, 0, (strlen($text) - $pad));
    }

    /**
     * 设置block size
     *
     * @param integer $blockSize
     */
    static public function setBlockSize($blockSize)
    {
        self::$blockSize = abs($blockSize);
    }
}