<?php

namespace EasyWeChat\MiniProgram\Shop\Account;

use EasyWeChat\Kernel\BaseClient;

/**
 * 自定义版交易组件及开放接口 - 商家入驻接口
 *
 * @package EasyWeChat\MiniProgram\Shop\Account
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class Client extends BaseClient
{
    /**
     * 获取商家类目列表
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCategoryList()
    {
        return $this->httpPostJson('shop/account/get_category_list');
    }

    /**
     * 获取商家品牌列表
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBrandList()
    {
        return $this->httpPostJson('shop/account/get_brand_list');
    }

    /**
     * 更新商家信息
     *
     * @param string $path 小程序path
     * @param string $phone 客服联系方式
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateInfo(string $path = '', string $phone = '')
    {
        return $this->httpPostJson('shop/account/update_info', [
            'service_agent_path' => $path,
            'service_agent_phone' => $phone,
        ]);
    }

    /**
     * 获取商家信息
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getInfo()
    {
        return $this->httpPostJson('shop/account/get_info');
    }
}
