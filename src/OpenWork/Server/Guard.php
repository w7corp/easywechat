<?php
/**
 * @link https://www.chaoyouyun.com
 * @copyright 2014-2018 Chongqing Xianzhou Technology LLC
 *
 * @author Xiaomin<keacefull@gmail.com>
 * @version 1.0.0
 * @since 1.0
 */

namespace EasyWeChat\OpenWork\Server;


use EasyWeChat\Kernel\ServerGuard;

class Guard extends ServerGuard
{
    protected $alwaysValidate = true;

    /**
     * @return bool
     */
    public function validate()
    {
        return $this;
    }

    /**
     * @return bool
     */
    protected function shouldReturnRawResponse(): bool
    {
        return !is_null($this->app['request']->get('echostr'));
    }

    protected function isSafeMode(): bool
    {
        return true;
    }
}
