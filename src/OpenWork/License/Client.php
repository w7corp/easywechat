<?php
/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\License;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Order Client
 *
 * @author moniang <me@imoniang.com>
 */
class Client extends BaseClient
{
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app, $app['provider_access_token']);
    }

    /**
     * 下单购买帐号
     *
     * 服务商下单为企业购买新的帐号，可以同时购买基础帐号与互通帐号。下单之后，需要到服务商管理端发起支付，支付完成之后，订单才能生效。
     *
     * @param string $corpId 企业id，只支持加密的corpid
     * @param array $data
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection SpellCheckingInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function create(string $corpId, array $data)
    {
        return $this->httpPostJson('cgi-bin/license/create_new_order', array_merge([
            'corpid' => $corpId
        ], $data));
    }

    /**
     * 创建续期任务
     *
     * 在同一个订单里，首次创建任务无须指定jobid，后续指定同一个jobid，表示往同一个订单任务追加续期的成员。
     *
     * @param string $corpId 企业id，只支持加密的corpid
     * @param array $data
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection SpellCheckingInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function renew(string $corpId, array $data)
    {
        return $this->httpPostJson('cgi-bin/license/create_renew_order_job', array_merge([
            'corpid' => $corpId
        ], $data));
    }

    /**
     * 提交续期订单
     *
     * 创建续期任务之后，需要调用该接口，以提交订单任务。注意，提交之后，需要到服务商管理端发起支付，支付完成之后，订单才能生效。
     *
     * @param string $jobId                 任务id
     * @param string $buyerUserId           下单人。服务商企业内成员userid。该userid必须登录过企业微信，并且企业微信已绑定微信
     * @param int    $accountDurationMonths 购买的月数，每个月按照31天计算。最多购买36个月。(若企业为服务商测试企业，每次续期只能续期1个月)
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection SpellCheckingInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function submitJob(string $jobId, string $buyerUserId, int $accountDurationMonths)
    {
        return $this->httpPostJson('cgi-bin/license/submit_order_job', [
            'jobid' => $jobId,
            'buyer_userid' => $buyerUserId,
            'account_duration' => [
                'months' => $accountDurationMonths
            ]
        ]);
    }

    /**
     * 获取订单列表
     *
     * 服务商查询自己某段时间内的平台能力服务订单列表
     *
     * @param string      $corpId     企业id，只支持加密的corpid。若指定corpid且corpid为服务商测试企业，则返回的订单列表为测试订单列表。否则只返回正式订单列表
     * @param int|null    $startTime  开始时间,下单时间。可不填。但是不能单独指定该字段，startTime跟endTime必须同时指定。
     * @param int|null    $endTime    结束时间,下单时间。起始时间跟结束时间不能超过31天。可不填。但是不能单独指定该字段，startTime跟endTime必须同时指定。
     * @param string|null $cursor     用于分页查询的游标，字符串类型，由上一次调用返回，首次调用可不填
     * @param int         $limit      返回的最大记录数，整型，最大值1000，默认值500
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection SpellCheckingInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function list(string $corpId, ?int $startTime = null, ?int $endTime = null, ?string $cursor = null, int $limit = 500)
    {
        return $this->httpPostJson('cgi-bin/license/list_order', [
            'corpid' => $corpId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'cursor' => $cursor,
            'limit' => $limit
        ]);
    }

    /**
     * 获取订单详情
     *
     * 查询某个订单的详情，包括订单的状态、基础帐号个数、互通帐号个数、帐号购买时长等。
     * 注意，该接口不返回订单中的帐号激活码列表或者续期的帐号成员列表，请调用{@see Client::getAccountList() 获取订单中的帐号列表}接口以获取帐号列表。
     *
     * @param string $orderId 订单id
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection SpellCheckingInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function get(string $orderId)
    {
        return $this->httpPostJson('cgi-bin/license/get_order', [
            'order_id' => $orderId
        ]);
    }

    /**
     * 获取订单中的帐号列表
     *
     * 查询指定订单下的平台能力服务帐号列表。若为购买帐号的订单或者存量企业的版本付费迁移订单，则返回帐号激活码列表；
     * 若为续期帐号的订单，则返回续期帐号的成员列表。
     *
     * 注意，若是购买帐号的订单，则仅订单支付完成时，系统才会生成帐号，故支付完成之前，该接口不会返回帐号激活码。
     *
     * @param string      $orderId 订单号
     * @param string|null $cursor  用于分页查询的游标，字符串类型，由上一次调用返回，首次调用可不填
     * @param int         $limit   返回的最大记录数，整型，最大值1000，默认值500
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection SpellCheckingInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getAccountList(string $orderId, ?string $cursor = null, int $limit = 500)
    {
        return $this->httpPostJson('cgi-bin/license/list_order_account', [
            'order_id' => $orderId,
            'limit' => $limit,
            'cursor' => $cursor
        ]);
    }

    /**
     * 取消订单
     *
     * 取消接口许可购买和续费订单，只可取消未支付且未失效的订单。
     *
     * @param string $corpId 企业id，只支持加密的corpid
     * @param string $orderId 订单号
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection SpellCheckingInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function cancel(string $corpId, string $orderId)
    {
        return $this->httpPostJson('cgi-bin/license/cancel_order', [
            'corpid' => $corpId,
            'order_id' => $orderId,
        ]);
    }
}
