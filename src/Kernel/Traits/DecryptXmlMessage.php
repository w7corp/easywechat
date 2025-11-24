<?php

namespace EasyWeChat\Kernel\Traits;

/**
 * @deprecated Use DecryptMessage trait instead. This trait will be removed in a future version.
 */
trait DecryptXmlMessage
{
    use DecryptMessage {
        decryptMessage as public;
    }
}
