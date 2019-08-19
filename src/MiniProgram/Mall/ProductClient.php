<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\Mall;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class ProductClient extends BaseClient
{
    /**
     * 更新或导入物品信息.
     *
     * @param array $params
     * @param bool  $isTest
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function import($params, $isTest = false)
    {
        return $this->httpPostJson('mall/importproduct', $params, ['is_test' => (int) $isTest]);
    }

    /**
     * 查询物品信息.
     *
     * @param array $params
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function query($params)
    {
        return $this->httpPostJson('mall/queryproduct', $params, ['type' => 'batchquery']);
    }

    /**
     * 小程序的物品是否可被搜索.
     *
     * @param bool $value
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setSearchable($value)
    {
        return $this->httpPostJson('mall/brandmanage', ['can_be_search' => $value], ['action' => 'set_biz_can_be_search']);
    }
}
