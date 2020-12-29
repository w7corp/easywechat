<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Setting;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author ClouderSky <clouder.flow@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 获取账号可以设置的所有类目.
     */
    public function getAllCategories()
    {
        return $this->httpPostJson('cgi-bin/wxopen/getallcategories');
    }

    /**
     * 添加类目.
     *
     * @param array $categories 类目数组
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addCategories(array $categories)
    {
        $params = ['categories' => $categories];

        return $this->httpPostJson('cgi-bin/wxopen/addcategory', $params);
    }

    /**
     * 删除类目.
     *
     * @param int $firstId  一级类目ID
     * @param int $secondId 二级类目ID
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteCategories(int $firstId, int $secondId)
    {
        $params = ['first' => $firstId, 'second' => $secondId];

        return $this->httpPostJson('cgi-bin/wxopen/deletecategory', $params);
    }

    /**
     * 获取账号已经设置的所有类目.
     */
    public function getCategories()
    {
        return $this->httpPostJson('cgi-bin/wxopen/getcategory');
    }

    /**
     * 修改类目.
     *
     * @param array $category 单个类目
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateCategory(array $category)
    {
        return $this->httpPostJson('cgi-bin/wxopen/modifycategory', $category);
    }

    /**
     * 小程序名称设置及改名.
     *
     * @param string $nickname       昵称
     * @param string $idCardMediaId  身份证照片素材ID
     * @param string $licenseMediaId 组织机构代码证或营业执照素材ID
     * @param array  $otherStuffs    其他证明材料素材ID
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setNickname(
        string $nickname,
        string $idCardMediaId = '',
        string $licenseMediaId = '',
        array $otherStuffs = []
    ) {
        $params = [
            'nick_name' => $nickname,
            'id_card' => $idCardMediaId,
            'license' => $licenseMediaId,
        ];

        for ($i = \count($otherStuffs) - 1; $i >= 0; --$i) {
            $params['naming_other_stuff_'.($i + 1)] = $otherStuffs[$i];
        }

        return $this->httpPostJson('wxa/setnickname', $params);
    }

    /**
     * 小程序改名审核状态查询.
     *
     * @param int $auditId 审核单id
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNicknameAuditStatus($auditId)
    {
        $params = ['audit_id' => $auditId];

        return $this->httpPostJson('wxa/api_wxa_querynickname', $params);
    }

    /**
     * 微信认证名称检测.
     *
     * @param string $nickname 名称（昵称）
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function isAvailableNickname($nickname)
    {
        $params = ['nick_name' => $nickname];

        return $this->httpPostJson(
            'cgi-bin/wxverify/checkwxverifynickname',
            $params
        );
    }

    /**
     * 查询小程序是否可被搜索.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSearchStatus()
    {
        return $this->httpGet('wxa/getwxasearchstatus');
    }

    /**
     * 设置小程序可被搜素.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setSearchable()
    {
        return $this->httpPostJson('wxa/changewxasearchstatus', [
            'status' => 0,
        ]);
    }

    /**
     * 设置小程序不可被搜素.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setUnsearchable()
    {
        return $this->httpPostJson('wxa/changewxasearchstatus', [
            'status' => 1,
        ]);
    }

    /**
     * 获取展示的公众号信息.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDisplayedOfficialAccount()
    {
        return $this->httpGet('wxa/getshowwxaitem');
    }

    /**
     * 设置展示的公众号.
     *
     * @param string|bool $appid
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setDisplayedOfficialAccount($appid)
    {
        return $this->httpPostJson('wxa/updateshowwxaitem', [
            'appid' => $appid ?: null,
            'wxa_subscribe_biz_flag' => $appid ? 1 : 0,
        ]);
    }

    /**
     * 获取可以用来设置的公众号列表.
     *
     * @param int $page
     * @param int $num
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDisplayableOfficialAccounts(int $page, int $num)
    {
        return $this->httpGet('wxa/getwxamplinkforshow', [
            'page' => $page,
            'num' => $num,
        ]);
    }
}
