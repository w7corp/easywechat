<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Notice.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Notice;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;

/**
 * Class Notice.
 */
class Notice extends AbstractAPI
{
    /**
     * Default color.
     *
     * @var string
     */
    protected $defaultColor = '#173177';

    /**
     * Attributes.
     *
     * @var array
     */
    protected $message = [
        'touser' => '',
        'template_id' => '',
        'url' => '',
        'data' => [],
    ];

    /**
     * Required attributes.
     *
     * @var array
     */
    protected $required = ['touser', 'template_id'];

    /**
     * Message backup.
     *
     * @var array
     */
    protected $messageBackup;

    const API_SEND_NOTICE = 'https://api.weixin.qq.com/cgi-bin/message/template/send';
    const API_SET_INDUSTRY = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry';
    const API_ADD_TEMPLATE = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template';
    const API_GET_INDUSTRY = 'https://api.weixin.qq.com/cgi-bin/template/get_industry';
    const API_GET_ALL_PRIVATE_TEMPLATE = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template';
    const API_DEL_PRIVATE_TEMPLATE = 'https://api.weixin.qq.com/cgi-bin/template/del_private_template';

    /**
     * Notice constructor.
     *
     * @param \EasyWeChat\Core\AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        parent::__construct($accessToken);

        $this->messageBackup = $this->message;
    }

    /**
     * Set industry.
     *
     * @param int $industryOne
     * @param int $industryTwo
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function setIndustry($industryOne, $industryTwo)
    {
        $params = [
                   'industry_id1' => $industryOne,
                   'industry_id2' => $industryTwo,
                  ];

        return $this->parseJSON('json', [self::API_SET_INDUSTRY, $params]);
    }

    /**
     * Get industry.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getIndustry()
    {
        return $this->parseJSON('json', [self::API_GET_INDUSTRY]);
    }

    /**
     * Add a template and get template ID.
     *
     * @param string $shortId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function addTemplate($shortId)
    {
        $params = ['template_id_short' => $shortId];

        return $this->parseJSON('json', [self::API_ADD_TEMPLATE, $params]);
    }

    /**
     * Get private templates.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getPrivateTemplates()
    {
        return $this->parseJSON('json', [self::API_GET_ALL_PRIVATE_TEMPLATE]);
    }

    /**
     * Delete private template.
     *
     * @param string $templateId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function deletePrivateTemplate($templateId)
    {
        $params = ['template_id' => $templateId];

        return $this->parseJSON('json', [self::API_DEL_PRIVATE_TEMPLATE, $params]);
    }

    /**
     * Send a notice message.
     *
     * @param $data
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function send($data = [])
    {
        $params = array_merge($this->message, $data);

        foreach ($params as $key => $value) {
            if (in_array($key, $this->required, true) && empty($value) && empty($this->message[$key])) {
                throw new InvalidArgumentException("Attribute '$key' can not be empty!");
            }

            $params[$key] = empty($value) ? $this->message[$key] : $value;
        }

        $params['data'] = $this->formatData($params['data']);

        $this->message = $this->messageBackup;

        return $this->parseJSON('json', [static::API_SEND_NOTICE, $params]);
    }

    /**
     * Magic access..
     *
     * @param string $method
     * @param array  $args
     *
     * @return Notice
     */
    public function __call($method, $args)
    {
        $map = [
                'template' => 'template_id',
                'templateId' => 'template_id',
                'uses' => 'template_id',
                'to' => 'touser',
                'receiver' => 'touser',
                'url' => 'url',
                'link' => 'url',
                'data' => 'data',
                'with' => 'data',
                'formId' => 'form_id',
                'prepayId' => 'form_id',
               ];

        if (0 === stripos($method, 'with') && strlen($method) > 4) {
            $method = lcfirst(substr($method, 4));
        }

        if (0 === stripos($method, 'and')) {
            $method = lcfirst(substr($method, 3));
        }

        if (isset($map[$method])) {
            $this->message[$map[$method]] = array_shift($args);
        }

        return $this;
    }

    /**
     * Format template data.
     *
     * @param array $data
     *
     * @return array
     */
    protected function formatData($data)
    {
        $return = [];

        foreach ($data as $key => $item) {
            if (is_scalar($item)) {
                $value = $item;
                $color = $this->defaultColor;
            } elseif (is_array($item) && !empty($item)) {
                if (isset($item['value'])) {
                    $value = strval($item['value']);
                    $color = empty($item['color']) ? $this->defaultColor : strval($item['color']);
                } elseif (count($item) < 2) {
                    $value = array_shift($item);
                    $color = $this->defaultColor;
                } else {
                    list($value, $color) = $item;
                }
            } else {
                $value = 'error data item.';
                $color = $this->defaultColor;
            }

            $return[$key] = [
                'value' => $value,
                'color' => $color,
            ];
        }

        return $return;
    }
}
