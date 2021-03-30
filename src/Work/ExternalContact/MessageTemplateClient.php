<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\ExternalContact;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class MessageTemplateClient.
 *
 * @author ljyljy0211 <ljyljy0211@gmail.com>
 */
class MessageTemplateClient extends BaseClient
{
    /**
     * Required attributes.
     *
     * @var array
     */
    protected $required = ['title', 'url', 'pic_media_id', 'appid', 'page'];

    protected $textMessage = [
        'content' => '',
    ];

    protected $imageMessage = [
        'media_id' => '',
        'pic_url' => '',
    ];

    protected $linkMessage = [
        'title' => '',
        'picurl' => '',
        'desc' => '',
        'url' => '',
    ];

    protected $miniprogramMessage = [
        'title' => '',
        'pic_media_id' => '',
        'appid' => '',
        'page' => '',
    ];

    /**
     * 添加入群欢迎语素材.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92366
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function create(array $msgTemplate)
    {
        $params = $this->formatMessage($msgTemplate);

        return $this->httpPostJson('cgi-bin/externalcontact/group_welcome_template/add', $params);
    }

    /**
     * 编辑入群欢迎语素材.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92366
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function update(string $templateId, array $msgTemplate)
    {
        $params = $this->formatMessage($msgTemplate);
        $params = array_merge([
            'template_id' => $templateId,
        ], $params);
        return $this->httpPostJson('cgi-bin/externalcontact/group_welcome_template/edit', $params);
    }

    /**
     * 获取入群欢迎语素材.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92366
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function get(string $templateId)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/group_welcome_template/get', [
            'template_id' => $templateId,
        ]);
    }

    /**
     * 删除入群欢迎语素材.
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92366
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function delete(string $templateId)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/group_welcome_template/del', [
            'template_id' => $templateId,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @return array
     */
    protected function formatMessage(array $data = [])
    {
        $params = $data;

        if (!empty($params['text'])) {
            $params['text'] = $this->formatFields($params['text'], $this->textMessage);
        }

        if (!empty($params['image'])) {
            $params['image'] = $this->formatFields($params['image'], $this->imageMessage);
        }

        if (!empty($params['link'])) {
            $params['link'] = $this->formatFields($params['link'], $this->linkMessage);
        }

        if (!empty($params['miniprogram'])) {
            $params['miniprogram'] = $this->formatFields($params['miniprogram'], $this->miniprogramMessage);
        }

        return $params;
    }

    /**
     * @throws InvalidArgumentException
     * @return array
     */
    protected function formatFields(array $data = [], array $default = [])
    {
        $params = array_merge($default, $data);
        foreach ($params as $key => $value) {
            if (in_array($key, $this->required, true) && empty($value) && empty($default[$key])) {
                throw new InvalidArgumentException(sprintf('Attribute "%s" can not be empty!', $key));
            }

            $params[$key] = empty($value) ? $default[$key] : $value;
        }

        return $params;
    }
}
