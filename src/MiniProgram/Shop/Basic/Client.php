<?php

namespace EasyWeChat\MiniProgram\Shop\Basic;

use EasyWeChat\Kernel\BaseClient;

/**
 * 自定义版交易组件及开放接口 - 接入商品前必需接口
 *
 * @package EasyWeChat\MiniProgram\Shop\Basic
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class Client extends BaseClient
{
    /**
     * 获取商品类目
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCat()
    {
        return $this->httpPostJson('shop/cat/get');
    }

    /**
     * @param string $imageFilePath 图片文件路径
     * @param int $respType 返回类型
     * @param int $uploadType 上传类型，本地图片或者网络图片
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function imgUpload(string $imageFilePath, int $respType = 1, int $uploadType = 0)
    {
        if ($uploadType == 0) {
            return $this->httpUpload('shop/img/upload', [
                'media' => $imageFilePath,
            ], [
                'resp_type' => $respType,
            ]);
        } else {
            return $this->httpPostJson('shop/img/upload', [], [
                'img_url' => $imageFilePath,
                'resp_type' => $respType,
                'upload_type' => $uploadType
            ]);
        }
    }

    /**
     * 品牌审核
     *
     * @param array $brand 品牌信息
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function auditBrand(array $brand)
    {
        return $this->httpPostJson('shop/audit/audit_brand', [
            'audit_req' => $brand
        ]);
    }

    /**
     * 类目审核
     *
     * @param array $category 类目资质
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function auditCategory(array $category)
    {
        return $this->httpPostJson('shop/audit/audit_category', [
            'audit_req' => $category
        ]);
    }

    /**
     * 获取审核结果
     *
     * @param string $auditId 提交审核时返回的id
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function auditResult(string $auditId)
    {
        return $this->httpPostJson('shop/audit/result', [
            'audit_id' => $auditId
        ]);
    }

    /**
     * 获取小程序资质
     *
     * @param int $reqType
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMiniAppCertificate(int $reqType = 2)
    {
        return $this->httpPostJson('shop/audit/get_miniapp_certificate', [
            'req_type' => $reqType
        ]);
    }
}
