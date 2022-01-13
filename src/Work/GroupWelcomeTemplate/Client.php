<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\GroupWelcomeTemplate;

use EasyWeChat\Kernel\BaseClient;

/**
 * 入群欢迎语素材管理
 *
 * @package EasyWeChat\Work\GroupWelcomeTemplate\Client
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class Client extends BaseClient
{
    /**
     * 添加入群欢迎语素材
     *
     * @description 企业可通过此API向企业的入群欢迎语素材库中添加素材。每个企业的入群欢迎语素材库中，最多容纳100个素材。
     * @param array $data
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(array $data)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/group_welcome_template/add', $data);
    }


    /**
     * 编辑入群欢迎语素材
     *
     * @description 企业可通过此API编辑入群欢迎语素材库中的素材，且仅能够编辑调用方自己创建的入群欢迎语素材。
     * @param string $templateId 欢迎语素材id
     * @param array $data
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function edit(string $templateId, array $data)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/group_welcome_template/edit', array_merge(
            ['template_id' => $templateId],
            $data
        ));
    }

    /**
     * 获取入群欢迎语素材
     *
     * @description 企业可通过此API获取入群欢迎语素材。
     * @param string $templateId 欢迎语素材id
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $templateId)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/group_welcome_template/get', ['template_id' => $templateId]);
    }

    /**
     * 删除入群欢迎语素材
     *
     * @description 企业可通过此API删除入群欢迎语素材，且仅能删除调用方自己创建的入群欢迎语素材。
     * @param string $templateId 欢迎语素材id
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function del(string $templateId)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/group_welcome_template/del', ['template_id' => $templateId]);
    }
}
