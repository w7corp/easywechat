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
     * 获取账号基本信息.
     */
    public function getBasicInfo()
    {
        return $this->httpPostJson('cgi-bin/account/getaccountbasicinfo');
    }

    /**
     * 修改头像.
     *
     * @param string $media_id 头像素材media_id
     * @param int    $left     剪裁框左上角x坐标（取值范围：[0, 1]）
     * @param int    $top      剪裁框左上角y坐标（取值范围：[0, 1]）
     * @param int    $right    剪裁框右下角x坐标（取值范围：[0, 1]）
     * @param int    $bottom   剪裁框右下角y坐标（取值范围：[0, 1]）
     */
    public function modifyHeadImage(
        string $media_id,
        float $left = 0,
        float $top = 0,
        float $right = 1,
        float $bottom = 1
    ) {
        $params = [
            'head_img_media_id' => $media_id,
            'x1' => $left, 'y1' => $top, 'x2' => $right, 'y2' => $bottom,
        ];

        return $this->httpPostJson('cgi-bin/account/modifyheadimage', $params);
    }

    /**
     * 修改功能介绍.
     *
     * @param string $signature 功能介绍（简介）
     */
    public function modifySignature($signature)
    {
        $params = ['signature' => $signature];

        return $this->httpPostJson('cgi-bin/account/modifysignature', $params);
    }

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
     */
    public function addCategory(array $categories)
    {
        return $this->httpPostJson('cgi-bin/wxopen/addcategory', $categories);
    }

    /**
     * 删除类目.
     *
     * @param int $first_id  一级类目ID
     * @param int $second_id 二级类目ID
     */
    public function deleteCategory(int $first_id, int $second_id)
    {
        $params = ['first' => $first_id, 'second' => $second_id];

        return $this->httpPostJson('cgi-bin/wxopen/deletecategory', $params);
    }

    /**
     * 获取账号已经设置的所有类目.
     */
    public function getCategory()
    {
        return $this->httpPostJson('cgi-bin/wxopen/getcategory');
    }

    /**
     * 修改类目.
     *
     * @param array $category 单个类目
     */
    public function modifyCategory(array $category)
    {
        return $this->httpPostJson('cgi-bin/wxopen/modifycategory', $category);
    }

    /**
     * 小程序名称设置及改名.
     *
     * @param string $nickname   昵称
     * @param string $idcard     身份证照片素材ID
     * @param string $license    组织机构代码证或营业执照素材ID
     * @param string $stuff_list 其他证明材料素材ID
     */
    public function setNickname(
        string $nickname,
        string $idcard = '',
        string $license = '',
        array $stuff_list = []
    ) {
        $params = [
            'nick_name' => $nickname,
            'id_card' => $idcard,
            'license' => $license,
        ];

        for ($i = 0; $i < \count($stuff_list); ++$i) {
            $params['naming_other_stuff_'.($i + 1)] = $stuff_list[$i];
        }

        return $this->httpPostJson('wxa/setnickname', $params);
    }

    /**
     * 小程序改名审核状态查询.
     *
     * @param int $audit_id 审核单id
     */
    public function queryNickname($audit_id)
    {
        $params = ['audit_id' => $audit_id];

        return $this->httpPostJson('wxa/api_wxa_querynickname', $params);
    }

    /**
     * 微信认证名称检测.
     *
     * @param string $nickname 名称（昵称）
     */
    public function checkWxVerifyNickname($nickname)
    {
        $params = ['nick_name' => $nickname];

        return $this->httpPostJson(
            'cgi-bin/wxverify/checkwxverifynickname', $params);
    }
}
