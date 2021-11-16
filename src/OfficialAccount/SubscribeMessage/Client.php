<?php
/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\SubscribeMessage;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use ReflectionClass;

/**
 * Class Client.
 *
 * @author tegic <teg1c@foxmail.com>
 */
class Client extends BaseClient
{
    protected $message = [
        'touser' => '',
        'template_id' => '',
        'page' => '',
        'miniprogram' => '',
        'data' => [],
    ];

    /**
     * {@inheritdoc}.
     */
    protected $required = ['touser', 'template_id', 'data'];

    /**
     * Combine templates and add them to your personal template library under your account.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addTemplate(string $tid, array $kidList, string $sceneDesc = null)
    {
        $sceneDesc = $sceneDesc ?? '';
        $data = \compact('tid', 'kidList', 'sceneDesc');

        return $this->httpPost('wxaapi/newtmpl/addtemplate', $data);
    }

    /**
     * Delete personal template under account.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteTemplate(string $id)
    {
        return $this->httpPost('wxaapi/newtmpl/deltemplate', ['priTmplId' => $id]);
    }

    /**
     * Get the category of the applet account.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCategory()
    {
        return $this->httpGet('wxaapi/newtmpl/getcategory');
    }

    /**
     * Get keyword list under template title.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTemplateKeywords(string $tid)
    {
        return $this->httpGet('wxaapi/newtmpl/getpubtemplatekeywords', compact('tid'));
    }

    /**
     * Get the title of the public template under the category to which the account belongs.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTemplateTitles(array $ids, int $start = 0, int $limit = 30)
    {
        $ids = \implode(',', $ids);
        $query = \compact('ids', 'start', 'limit');

        return $this->httpGet('wxaapi/newtmpl/getpubtemplatetitles', $query);
    }

    /**
     * Get list of personal templates under the current account.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTemplates()
    {
        return $this->httpGet('wxaapi/newtmpl/gettemplate');
    }

    /**
     * Send a subscribe message.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(array $data = [])
    {
        $params = $this->formatMessage($data);

        $this->restoreMessage();

        return $this->httpPostJson('cgi-bin/message/subscribe/bizsend', $params);
    }

    /**
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function formatMessage(array $data = [])
    {
        $params = array_merge($this->message, $data);

        foreach ($params as $key => $value) {
            if (in_array($key, $this->required, true) && empty($value) && empty($this->message[$key])) {
                throw new InvalidArgumentException(sprintf('Attribute "%s" can not be empty!', $key));
            }

            $params[$key] = empty($value) ? $this->message[$key] : $value;
        }

        foreach ($params['data'] as $key => $value) {
            if (is_array($value)) {
                if (\array_key_exists('value', $value)) {
                    $params['data'][$key] = ['value' => $value['value']];

                    continue;
                }

                if (count($value) >= 1) {
                    $value = [
                        'value' => $value[0],
//                        'color' => $value[1],// color unsupported
                    ];
                }
            } else {
                $value = [
                    'value' => strval($value),
                ];
            }

            $params['data'][$key] = $value;
        }

        return $params;
    }

    /**
     * Restore message.
     */
    protected function restoreMessage()
    {
        $this->message = (new ReflectionClass(static::class))->getDefaultProperties()['message'];
    }
}
