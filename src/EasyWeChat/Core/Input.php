<?php

/**
 * Input.php.
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

namespace EasyWeChat\Core;

use EasyWeChat\Core\Exceptions\FaultException;
use EasyWeChat\Encryption\Cryptor;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\XML;

/**
 * Class Input.
 */
class Input extends Collection
{
    /**
     * Input encryption status.
     *
     * @var bool
     */
    protected $encrypted = false;

    /**
     * Constructor.
     *
     * @param string  $token
     * @param Cryptor $cryptor
     */
    public function __construct($token, Cryptor $cryptor)
    {
        parent::__construct($this->build($cryptor));
        $this->validate($token);
    }

    /**
     * Get encryption status.
     *
     * @return bool
     */
    public function isEncrypted()
    {
        return $this->encrypted;
    }

    /**
     * Set input form custom array or Collection.
     *
     * @param array|Collection $input
     */
    public function setInput($input)
    {
        if ($input instanceof Collection) {
            $input = $input->toArray();
        }

        parent::__construct($input);
    }

    /**
     * Build input.
     *
     * @param Cryptor $cryptor
     *
     * @return array
     */
    protected function build($cryptor)
    {
        $xml = file_get_contents('php://input');

        $input = XML::parse($xml);

        if (empty($_REQUEST['echostr'])
            && !empty($_REQUEST['encrypt_type'])
            && $_REQUEST['encrypt_type'] === 'aes'
        ) {
            $this->encrypted = true;

            $input = $cryptor->decryptMsg(
                $_REQUEST['msg_signature'],
                $_REQUEST['nonce'],
                $_REQUEST['timestamp'],
                $xml
            );
        }

        /* @var array $input */
        return array_merge($_REQUEST, (array) $input);
    }

    /**
     * Validation request params.
     *
     * @param string $token
     *
     * @throws FaultException
     */
    protected function validate($token)
    {
        $input = [
            $token,
            $this->get('timestamp'),
            $this->get('nonce'),
        ];

        if ($this->has('signature')
            && $this->signature($input) !== $this->get('signature')
        ) {
            throw new FaultException('Invalid request signature.', 400);
        }
    }

    /**
     * Get signature.
     *
     * @param array $input
     *
     * @return string
     */
    protected function signature($input)
    {
        sort($input, SORT_STRING);

        return sha1(implode($input));
    }
}//end class
