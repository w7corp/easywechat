<?php
/**
 * Created by PhpStorm.
 * User: wangshukai
 * Date: 2021/3/5
 * Time: 1:55 PM
 */

namespace EasyWeChat\MiniProgram\Transactions;

use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\MiniProgram\Core\AbstractMiniProgram;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;

class Transactions extends AbstractMiniProgram
{


    const API_GET_REGISTER = 'https://api.weixin.qq.com/shop/register/apply';
    const API_GET_CHECK_REGISTER = 'https://api.weixin.qq.com/shop/register/check';
    const API_GET_CLASS_ALL = 'https://api.weixin.qq.com/shop/cat/get';
    const API_UPLOAD_IMG = 'https://api.weixin.qq.com/shop/img/upload';
    const API_SHOP_AUDIT = 'https://api.weixin.qq.com/shop/audit/audit_brand';
    const API_SHOP_AUDIT_CATEGORY = 'https://api.weixin.qq.com/shop/audit/audit_category';
    const API_GET_MINIAPP_CERTIFICATE = 'https://api.weixin.qq.com/shop/audit/get_miniapp_certificate';


    public function shopRegister(int $actionType)
    {
        $params = ["action_type" => $actionType];
        return $this->getStream(self::API_GET_REGISTER, $params);
    }

    public function checkRegister()
    {
        return $this->getStream(self::API_GET_CHECK_REGISTER, []);
    }

    public function getAllClassification()
    {
        return $this->getStream(self::API_GET_CLASS_ALL, []);
    }

    public function auditBrand(array $params)
    {
        return $this->getStream(self::API_SHOP_AUDIT, $params);
    }

    public function auditCategory(array $params)
    {
        return $this->getStream(self::API_SHOP_AUDIT_CATEGORY, $params);

    }

    public function getMiniappCertificate(int $reqType)
    {
        $params = ["req_type" => $reqType];
        return $this->getStream(self::API_GET_MINIAPP_CERTIFICATE, $params);

    }

    /**
     * @param $path
     * @return \EasyWeChat\Support\Collection
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function uploadImg($path)
    {
        return $this->uploadMedia('image', $path);
    }

    /**
     * Upload material.
     *
     * @param string $type
     * @param string $path
     * @param array $form
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws InvalidArgumentException
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    protected function uploadMedia($type, $path, array $form = [])
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }
        $form['type'] = $type;

        return $this->parseJSON('upload', [$this->getAPIByType($type), ['media' => $path], $form]);
    }

    /**
     * Get stream.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    protected function getStream($endpoint, $params)
    {
        return json_decode(strval($this->getHttp()->json($endpoint, $params)->getBody()), true);
    }

    /**
     * Get API by type.
     *
     * @param string $type
     *
     * @return string
     */
    public function getAPIByType($type)
    {
        switch ($type) {
            case 'image':
                $api = self::API_UPLOAD_IMG;

                break;
            default:
                $api = self::API_UPLOAD_IMG;
        }

        return $api;
    }
}