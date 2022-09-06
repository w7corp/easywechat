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
 * License Account Client
 *
 * @author moniang <me@imoniang.com>
 */
class Account extends BaseClient
{
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app, $app['provider_access_token']);
    }

    /**
     * 激活帐号
     *
     * >下单购买帐号并支付完成之后，先调用{@see Client::getAccountList() 获取订单中的帐号列表}接口获取到帐号激活码，
     * 然后可以调用该接口将激活码绑定到某个企业员工，以对其激活相应的平台服务能力。
     *
     * **一个userid允许激活一个基础帐号以及一个互通帐号。**
     *
     * @param string $activeCode 帐号激活码
     * @param string $corpId     待绑定激活的成员所属企业corpId，只支持加密的corpId
     * @param string $userId     待绑定激活的企业成员userId 。只支持加密的userId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection SpellCheckingInspection
     */
    public function active(string $activeCode, string $corpId, string $userId)
    {
        return $this->httpPostJson('cgi-bin/license/active_account', [
            'active_code' => $activeCode,
            'corpid' => $corpId,
            'userid' => $userId
        ]);
    }

    /**
     * 批量激活帐号
     *
     * >可在一次请求里为一个企业的多个成员激活许可帐号，便于服务商批量化处理。
     *
     * **一个userid允许激活一个基础帐号以及一个互通帐号。单次激活的员工数量不超过1000**
     *
     * @param string $corpId    待绑定激活的成员所属企业corpid，只支持加密的corpid
     * @param array $activeList 需要激活的帐号列表,每个数组包含<b>active_code</b>帐号激活码和<b>userid</b>待绑定激活的企业成员加密userid
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection SpellCheckingInspection
     */
    public function batchActive(string $corpId, array $activeList)
    {
        return $this->httpPostJson('cgi-bin/license/batch_active_account', [
            'corpid' => $corpId,
            'active_list' => $activeList
        ]);
    }

    /**
     * 获取激活码详情
     *
     * >查询某个帐号激活码的状态以及激活绑定情况。
     *
     * @param string $corpId
     * @param string $activeCode
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection SpellCheckingInspection
     */
    public function getActiveCodeInfo(string $corpId, string $activeCode)
    {
        return $this->httpPostJson('cgi-bin/license/get_active_info_by_code', [
            'corpid' => $corpId,
            'active_code' => $activeCode
        ]);
    }

    /**
     * 批量获取激活码详情
     *
     * >批量查询帐号激活码的状态以及激活绑定情况。
     *
     * @param string   $corpId         要查询的企业的corpid，只支持加密的corpid
     * @param string[] $activeCodeList 激活码列表，最多不超过1000个
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection SpellCheckingInspection
     */
    public function batchGetActiveCodeInfo(string $corpId, array $activeCodeList)
    {
        return $this->httpPostJson('cgi-bin/license/batch_get_active_info_by_code', [
            'corpid' => $corpId,
            'active_code_list' => $activeCodeList
        ]);
    }

    /**
     * 获取企业的帐号列表
     *
     * >查询指定企业下的平台能力服务帐号列表。
     *
     * @param string      $corpId 企业corpId ，只支持加密的corpId
     * @param string|null $cursor 返回的最大记录数，整型，最大值1000，默认值500
     * @param int         $limit  用于分页查询的游标，字符串类型，由上一次调用返回，首次调用可不填
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection SpellCheckingInspection
     */
    public function list(string $corpId, ?string $cursor = null, int $limit = 500)
    {
        return $this->httpPostJson('cgi-bin/license/list_actived_account', [
            'corpid' => $corpId,
            'limit' => $limit,
            'cursor' => $cursor
        ]);
    }

    /**
     * 获取成员的激活详情
     *
     * >查询某个企业成员的激活情况。
     *
     * @param string $corpId 企业corpId ，只支持加密的corpId
     * @param string $userId 待查询员工的userid，只支持加密的userid
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection SpellCheckingInspection
     */
    public function getActiveAccountInfo(string $corpId, string $userId)
    {
        return $this->httpPostJson('cgi-bin/license/get_active_info_by_user', [
            'corpid' => $corpId,
            'userid' => $userId
        ]);
    }


    /**
     * 帐号继承
     *
     * >在企业员工离职或者工作范围的有变更时，允许将其许可帐号继承给其他员工。
     *
     * **调用限制:**
     * - 转移成员和接收成员属于同一个企业
     * - 转移成员的帐号已激活，且在有效期
     * - 转移许可的成员为离职成员时，不限制下次转接的时间间隔
     * - 转移许可的成员为在职成员时，转接后30天后才可进行下次转接
     * - 接收成员许可不能与转移成员的许可重叠（同时拥有基础帐号或者互通帐号）
     * - 单次转移的帐号数限制在1000以内
     *
     * @param string $corpId       待绑定激活的成员所属企业corpId，只支持加密的corpId
     * @param array  $transferList 待转移成员列表，每个数组包含<b>handover_userid</b>转移成员的userid和<b>takeover_userid</b>接收成员的userid
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection SpellCheckingInspection
     */
    public function batchTransfer(string $corpId, array $transferList)
    {
        return $this->httpPostJson('cgi-bin/license/batch_transfer_license', [
            'corpid' => $corpId,
            'transfer_list' => $transferList
        ]);
    }

    /**
     * 分配激活码给下游企业
     *
     * >服务商可调用该接口将为上游企业购买的激活码分配给下游企业使用。
     *
     * @param string $fromCorpid 上游企业corpid。支持明文或者密文的corpid
     * @param string $toCorpid   下游企业corpid。支持明文或者密文的corpid
     * @param array  $activeCode 分享的接口许可激活码。单次分享激活码不可超过1000个，累计分享给同一下游企业的激活码总数不可超过上下游通讯录中该下游企业人数的2倍
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection SpellCheckingInspection
     */
    public function share(string $fromCorpid, string $toCorpid, array $activeCode)
    {
        return $this->httpPostJson('cgi-bin/license/batch_share_active_code', [
            'from_corpid' => $fromCorpid,
            'to_corpid' => $toCorpid,
            'share_list' => array_map(function ($code) {
                return [
                    'active_code' => $code,
                ];
            }, $activeCode),
        ]);
    }
}
