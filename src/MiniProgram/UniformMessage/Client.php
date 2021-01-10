<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\UniformMessage;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\OfficialAccount\TemplateMessage\Client as BaseClient;

class Client extends BaseClient
{
    public const API_SEND = 'cgi-bin/message/wxopen/template/uniform_send';

    /**
     * {@inheritdoc}.
     *
     * @var array
     */
    protected array $message = [
        'touser' => '',
    ];

    /**
     * Weapp Attributes.
     *
     * @var array
     */
    protected array $weappMessage = [
        'template_id' => '',
        'page' => '',
        'form_id' => '',
        'data' => [],
        'emphasis_keyword' => '',
    ];

    /**
     * Official account attributes.
     *
     * @var array
     */
    protected array $mpMessage = [
        'appid' => '',
        'template_id' => '',
        'url' => '',
        'miniprogram' => [],
        'data' => [],
    ];

    /**
     * Required attributes.
     *
     * @var array
     */
    protected array $required = ['touser', 'template_id', 'form_id', 'miniprogram', 'appid'];

    /**
     * @param array $data
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function formatMessage(array $data = [])
    {
        $params = array_merge($this->message, $data);

        if (empty($params['touser'])) {
            throw new InvalidArgumentException(sprintf('Attribute "touser" can not be empty!'));
        }

        if (!empty($params['weapp_template_msg'])) {
            $params['weapp_template_msg'] = $this->formatWeappMessage($params['weapp_template_msg']);
        }

        if (!empty($params['mp_template_msg'])) {
            $params['mp_template_msg'] = $this->formatMpMessage($params['mp_template_msg']);
        }

        return $params;
    }

    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function formatWeappMessage(array $data = [])
    {
        $params = $this->baseFormat($data, $this->weappMessage);

        $params['data'] = $this->formatData($params['data'] ?? []);

        return $params;
    }

    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function formatMpMessage(array $data = [])
    {
        $params = $this->baseFormat($data, $this->mpMessage);

        if (empty($params['miniprogram']['appid'])) {
            $params['miniprogram']['appid'] = $this->app['config']['app_id'];
        }

        $params['data'] = $this->formatData($params['data'] ?? []);

        return $params;
    }

    /**
     * @param array $data
     * @param array $default
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function baseFormat($data = [], $default = [])
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
