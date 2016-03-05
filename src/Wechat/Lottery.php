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
 * LuckMoney.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author bontian <bontian@163.com>
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\SignGenerator;

class Lottery
{
    /**
     * 创建红包活动API.
     */
    const API_CREATE = 'https://api.weixin.qq.com/shakearound/lottery/addlotteryinfo';

    /**
     * 录入红包信息API.
     */
    const API_SET_INFO = 'https://api.weixin.qq.com/shakearound/lottery/setprizebucket';

    /**
     * 设置红包活动开关状态API.
     */
    const API_SET_STATUS = 'https://api.weixin.qq.com/shakearound/lottery/setlotteryswitch';

    /**
     * 查询红包活动信息API.
     */
    const API_QUERY = 'https://api.weixin.qq.com/shakearound/lottery/querylottery';

    private $http;

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 创建红包活动.
     *
     * <pre>
     * $data:
     * {
     *     "title": "活动标题",
     *     "desc": "活动描述",
     *     "onoff": 1,
     *     "begin_time": 1400000000,
     *     "expire_time": 1400000000,
     *     "re_openid": "oJCvDjjQKx5LMtM_1kjK0gGQLsew",
     *     "sponsor_appid": 'wx1234123412',
     *     "total": 100,
     *     "jump_url": "http://www.xxx.com",
     *     "key": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
     * }
     * </pre>
     *
     * @param array  $data
     * @param int    $useTemplate
     * @param string $logoUrl
     *
     * @return mixed
     * 
     * @throws \Overtrue\Wechat\Exception
     */
    public function create(array $data, $useTemplate = 1, $logoUrl = '')
    {
        if ($useTemplate == 1 && empty($logoUrl)) {
            throw new Exception('logo_url is required when using template.');
        }

        $url = self::API_CREATE."?use_template={$useTemplate}&logo_url={$logoUrl}";

        return $this->http->jsonPost($url, $data);
    }

    /**
     * 设置红包活动信息.
     *
     * <pre>
     * $data:
     * {
     *     "lottery_id": "peDVUNPHSfXdM707opOlpQ",
     *     "mchid":"10054123",
     *     "sponsor_appid": "wx1113314243",
     *     "prize_info_list": [
     *          {
     *              'ticket':'ticket1',
     *              'ticket':'ticket2',
     *              'ticket':'ticket3'...
     *          }
     *     ],
     *     "ticket": "xxxxxxxx",
     * }
     * </pre>
     *
     * @param array $data
     * @param int   $type
     *
     * @return array
     */
    public function setInfo(array $data)
    {
        return $this->http->jsonPost(self::API_SET_INFO, $data);
    }

    /**
     * 查询红包活动信息.
     *
     * @param string $lotteryId
     *
     * @return array
     */
    public function query($lotteryId)
    {
        return $this->http->get(self::API_QUERY, array('lottery_id' => $lotteryId));
    }

    /**
     * 设置红包活动状态
     *
     * @param string $lotteryId 红包活动ID
     * @param int    $status    状态，0关闭，1开启
     *
     * @return array
     */
    public function setStatus($lotteryId, $status)
    {
        $params = array(
            'lottery_id' => $lotteryId,
            'onoff' => $status,
        );

        return $this->http->get(self::API_SET_STATUS, $params);
    }

    /**
     * 开启红包活动.
     *
     * @param string $lotteryId 红包活动ID
     *
     * @return array
     */
    public function switchOn($lotteryId)
    {
        return $this->setStatus($lotteryId, 1);
    }

    /**
     * 关闭红包活动.
     *
     * @param string $lotteryId 红包活动ID
     *
     * @return array
     */
    public function switchOff($lotteryId)
    {
        return $this->setStatus($lotteryId, 0);
    }

    /**
     * 生成用于摇红包JSAPI调用的数据.
     *
     * @param string $lotteryId
     * @param string $openId
     * @param string $key
     *
     * @return string
     */
    public function getJsPackage($lotteryId, $openId, $key)
    {
        $param['noncestr'] = uniqid('pre_');
        $param['lottery_id'] = $lotteryId;
        $param['openid'] = $openId;

        $signGenerator = new SignGenerator($param);

        $signGenerator->onSortAfter(function (SignGenerator $that) use ($key) {
            $that->key = $key;
        });

        $sign = $signGenerator->getResult();

        $param['sign'] = $sign;

        return json_encode($param);
    }
}
