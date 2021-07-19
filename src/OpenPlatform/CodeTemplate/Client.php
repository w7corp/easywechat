<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\CodeTemplate;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author caikeal <caiyuezhang@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 获取草稿箱内的所有临时代码草稿
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDrafts()
    {
        return $this->httpGet('wxa/gettemplatedraftlist');
    }

    /**
     * 将草稿箱的草稿选为小程序代码模版.
     *
     * @param int $draftId
     * @param int $templateType
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createFromDraft(int $draftId, int $templateType = 0)
    {
        $params = [
            'draft_id' => $draftId,
            'template_type' => $templateType,
        ];

        return $this->httpPostJson('wxa/addtotemplate', $params);
    }

    /**
     * 获取代码模版库中的所有小程序代码模版.
     *
     * @param int $templateType
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(int $templateType = null)
    {
        $params = [
            'template_type' => $templateType,
        ];

        return $this->httpGet('wxa/gettemplatelist', $params);
    }

    /**
     * 删除指定小程序代码模版.
     *
     * @param string $templateId
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($templateId)
    {
        $params = [
            'template_id' => $templateId,
        ];

        return $this->httpPostJson('wxa/deletetemplate', $params);
    }
}
