<?php

/**
 * Notice.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Notice;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Http;

/**
 * Class Notice.
 */
class Notice
{
    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

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
                          'topcolor' => '#FF0000',
                          'data' => [],
                         ];
    /**
     * Message backup.
     *
     * @var array
     */
    protected $messageBackup;

    const API_SEND_NOTICE = 'https://api.weixin.qq.com/cgi-bin/message/template/send';
    const API_SET_INDUSTRY = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry';
    const API_ADD_TEMPLATE = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template';

    /**
     * Constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http->setExpectedException(NoticeHttpException::class);
        $this->messageBackup = $this->message;
    }

    /**
     * Set industry.
     *
     * @param int $industryOne
     * @param int $industryTwo
     *
     * @return bool
     */
    public function setIndustry($industryOne, $industryTwo)
    {
        $params = [
                   'industry_id1' => $industryOne,
                   'industry_id2' => $industryTwo,
                  ];

        return $this->http->json(self::API_SET_INDUSTRY, $params);
    }

    /**
     * Add a template and get template ID.
     *
     * @param string $shortId
     *
     * @return string
     */
    public function addTemplate($shortId)
    {
        $params = ['template_id_short' => $shortId];

        $result = $this->http->json(self::API_ADD_TEMPLATE, $params);

        return $result['template_id'];
    }

    /**
     * Send a notice message.
     *
     * @param string $to
     * @param string $templateId
     * @param array  $data
     * @param string $url
     * @param string $color
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    public function send(
        $to = null,
        $templateId = null,
        array $data = [],
        $url = null,
        $color = null
    ) {
        $params = [
                   'touser' => $to,
                   'template_id' => $templateId,
                   'url' => $url,
                   'topcolor' => $color,
                   'data' => $data,
                  ];

        $required = [
                     'touser',
                     'template_id',
                    ];

        foreach ($params as $key => $value) {
            if (in_array($key, $required) && empty($value) && empty($this->message[$key])) {
                throw new InvalidArgumentException("Attibute '$key' can not be empty!");
            }

            $params[$key] = empty($value) ? $this->message[$key] : $value;
        }

        $params['data'] = $this->formatData($params['data']);

        $result = $this->http->json(self::API_SEND_NOTICE, $params);
        $this->message = $this->messageBackup;

        return $result['msgid'];
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
                'to' => 'touser',
                'receiver' => 'touser',
                'color' => 'topcolor',
                'topColor' => 'topcolor',
                'url' => 'url',
                'link' => 'url',
                'data' => 'data',
                'with' => 'data',
               ];

        if (0 === stripos($method, 'with')) {
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
