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
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\MiniProgram\SubscribeMessage;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;

class SubscribeMessage extends AbstractAPI
{
    /**
     * {@inheritdoc}.
     */
    protected $message = [
        'touser' => '',
        'template_id' => '',
        'page' => '',
        'data' => [],
    ];

    /**
     * Message backup.
     *
     * @var array
     */
    protected $messageBackup;

    /**
     * {@inheritdoc}.
     */
    protected $required = ['touser', 'template_id', 'data'];

    /**
     * Send notice message.
     */
    const API_SEND_MESSAGE = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send';
    const API_ADD_TEMPLATE = 'https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate';
    const API_DELETE_TEMPLATE = 'https://api.weixin.qq.com/wxaapi/newtmpl/deltemplate';
    const API_GET_PUB_TEMPLATE_KEYWORDS = 'https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatekeywords';
    const API_GET_PUB_TEMPLATE_TITLES = 'https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatetitles';
    const API_GET_TEMPLATE = 'https://api.weixin.qq.com/wxaapi/newtmpl/gettemplate';
    const API_GET_CATEGORY = 'https://api.weixin.qq.com/wxaapi/newtmpl/getcategory';

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

        return $this->parseJSON('json', [static::API_SEND_MESSAGE, $params]);
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
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (\array_key_exists('value', $value)) {
                    $return[$key] = ['value' => $value['value']];
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

            $return[$key] = $value;
        }
        return $return;
    }


    /**
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.addTemplate.html
     * Combine templates and add them to your personal template library under your account.
     * @param $tid
     * @param $kidList
     * @param null $sceneDesc
     * @return \EasyWeChat\Support\Collection|null
     */
    public function addTemplate($tid, $kidList, $sceneDesc = null)
    {
        $sceneDesc = $sceneDesc ? $sceneDesc :  '';
        $params = \compact('tid', 'kidList', 'sceneDesc');

        return $this->parseJSON('json', [static::API_ADD_TEMPLATE, $params]);
    }

    /**
     * Delete personal template under account.
     * @param $id
     * @return \EasyWeChat\Support\Collection|null
     */
    public function deleteTemplate($id)
    {
        return $this->parseJSON('json', [static::API_DELETE_TEMPLATE, ['priTmplId' => $id]]);
    }

    /**
     * Get keyword list under template title.
     *
     * @param string $tid
     * @return \EasyWeChat\Support\Collection|null
     */
    public function getTemplateKeywords($tid)
    {
        return $this->parseJSON('get', [static::API_GET_PUB_TEMPLATE_KEYWORDS, compact('tid')]);
    }

    /**
     * Get the title of the public template under the category to which the account belongs.
     * @param array $ids
     * @param int $start
     * @param int $limit
     * @return \EasyWeChat\Support\Collection|null
     */
    public function getTemplateTitles($ids, $start = 0, $limit = 30)
    {
        $ids = \implode(',', $ids);
        $query = \compact('ids', 'start', 'limit');
        return $this->parseJSON('get', [static::API_GET_PUB_TEMPLATE_TITLES, $query]);
    }


    /**
     * Get list of personal templates under the current account.
     * @return \EasyWeChat\Support\Collection|null
     */
    public function getTemplates()
    {
        return $this->parseJSON('get', [static::API_GET_TEMPLATE]);
    }

    /**
     * Get the category of the applet account.
     * @return \EasyWeChat\Support\Collection|null
     */
    public function getCategory()
    {
        return $this->parseJSON('get', [static::API_GET_CATEGORY]);
    }
}
