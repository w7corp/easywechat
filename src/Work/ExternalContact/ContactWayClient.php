<?php

declare(strict_types=1);

namespace EasyWeChat\Work\ExternalContact;

use EasyWeChat\Kernel\BaseClient;

class ContactWayClient extends BaseClient
{
    /**
     * 配置客户联系「联系我」方式.
     *
     * @param int   $type
     * @param int   $scene
     * @param array $config
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(int $type, int $scene, array $config = [])
    {
        $params = array_merge([
            'type' => $type,
            'scene' => $scene,
        ], $config);

        return $this->httpPostJson('cgi-bin/externalcontact/add_contact_way', $params);
    }

    /**
     * 获取企业已配置的「联系我」方式.
     *
     * @param string $configId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $configId)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/get_contact_way', [
            'config_id' => $configId,
        ]);
    }

    /**
     * 更新企业已配置的「联系我」方式.
     *
     * @param string $configId
     * @param array  $config
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $configId, array $config = [])
    {
        $params = array_merge([
            'config_id' => $configId,
        ], $config);

        return $this->httpPostJson('cgi-bin/externalcontact/update_contact_way', $params);
    }

    /**
     * 删除企业已配置的「联系我」方式.
     *
     * @param string $configId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $configId)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/del_contact_way', [
            'config_id' => $configId,
        ]);
    }
}
