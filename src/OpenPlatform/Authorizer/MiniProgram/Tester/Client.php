<?php
/**
 * Created by PhpStorm.
 * User: keal
 * Date: 2017/11/6
 * Time: 下午2:11
 */

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Tester;


use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client
 *
 * @author caikeal <caiyuezhang@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 绑定小程序体验者
     *
     * @param string $wechatId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function bind(string $wechatId)
    {
        return $this->httpPostJson('wxa/bind_tester', [
            'wechatid' => $wechatId
        ]);
    }

    /**
     * 解绑小程序体验者
     *
     * @param string $wechatId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function unbind(string $wechatId)
    {
        return $this->httpPostJson('wxa/unbind_tester', [
            'wechatid' => $wechatId
        ]);
    }
}