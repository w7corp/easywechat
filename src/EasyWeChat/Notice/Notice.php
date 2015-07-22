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

/**
 * 模板消息.
 */
class Notice
{
    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * 默认数据项颜色.
     *
     * @var string
     */
    protected $defaultColor = '#173177';

    /**
     * 消息属性.
     *
     * @var array
     */
    protected $message = [
                          'touser' => '',
                          'template_id' => '',
                          'url' => '',
                          'topcolor' => '#FF00000',
                          'data' => [],
                         ];

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
        $this->http = $http->setExpectedException('EasyWeChat\Notice\NoticeHttpException');
    }

    /**
     * 修改账号所属行业.
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

        return $this->http->jsonPost(self::API_SET_INDUSTRY, $params);
    }

    /**
     * 添加模板并获取模板ID.
     *
     * @param string $shortId
     *
     * @return string
     */
    public function addTemplate($shortId)
    {
        $params = ['template_id_short' => $shortId];

        $result = $this->http->jsonPost(self::API_ADD_TEMPLATE, $params);

        return $result['template_id'];
    }

    /**
     * 发送模板消息.
     *
     * @param string $to
     * @param string $templateId
     * @param array  $data
     * @param string $url
     * @param string $color
     *
     * @return int
     *
     * @throws Exception
     */
    public function send(
        $to = null,
        $templateId = null,
        array $data = [],
        $url = null,
        $color = '#FF0000'
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
                throw new Exception("消息属性 '$key' 不能为空！");
            }

            $params[$key] = empty($value) ? $this->message[$key] : $value;
        }

        $params['data'] = $this->formatData($params['data']);

        $result = $this->http->jsonPost(self::API_SEND_NOTICE, $params);

        return $result['msgid'];
    }

    /**
     * 设置模板消息数据项的默认颜色.
     *
     * @param string $color
     */
    public function defaultColor($color)
    {
        $this->defaultColor = $color;
    }

    /**
     * 格式化模板数据.
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
                    list($value, $color) = each($item);
                }
            } else {
                $value = '数据项格式错误';
                $color = $this->defaultColor;
            }

            $return[$key] = [
                             'value' => $value,
                             'color' => $color,
                            ];
        }

        return $return;
    }

    /**
     * 魔术调用.
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
                'uses' => 'template_id',
                'templateId' => 'template_id',
                'to' => 'touser',
                'receiver' => 'touser',
                'color' => 'topcolor',
                'topColor' => 'topcolor',
                'url' => 'url',
                'linkTo' => 'linkTo',
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

            return $this;
        }
    }
}//end class

