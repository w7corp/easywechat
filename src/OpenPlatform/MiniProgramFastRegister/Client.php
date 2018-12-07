<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\MiniProgramFastRegister;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client
 *
 * @author dudashuang <dudashuang1222@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 快速创建小程序
     *
     * @param string $companyName
     * @param string $companyCode
     * @param int $codeType
     * @param string $legalPersonaWechat
     * @param string $legalPersonaName
     * @param string $componentPhone
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function create(string $companyName, string $companyCode, int $codeType, string $legalPersonaWechat, string $legalPersonaName, string $componentPhone)
    {
        $params = [
            'name'                 => $companyName,
            'code'                 => $companyCode,
            'code_type'            => $codeType,
            'legal_persona_wechat' => $legalPersonaWechat,
            'legal_persona_name'   => $legalPersonaName,
            'component_phone'      => $componentPhone,
        ];

        return $this->httpPostJson('cgi-bin/component/fastregisterweapp', $params, ['action' => 'create']);
    }

    /**
     * 查询创建任务状态
     *
     * @param string $companyName
     * @param string $legalPersonaWechat
     * @param string $legalPersonaName
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function search(string $companyName, string $legalPersonaWechat, string $legalPersonaName)
    {
        $params = [
            'name'                 => $companyName,
            'legal_persona_wechat' => $legalPersonaWechat,
            'legal_persona_name'   => $legalPersonaName,
        ];

        return $this->httpPostJson('cgi-bin/component/fastregisterweapp', $params, ['action' => 'search']);
    }
}
