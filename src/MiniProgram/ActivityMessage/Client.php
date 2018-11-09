<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\ActivityMessage;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

class Client extends BaseClient
{

    /**
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function createActivityId()
    {
        return $this->httpGet('cgi-bin/message/wxopen/activityid/create');
    }

    /**
     * @param $activityId
     * @param int $state
     * @param array $parameter
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws InvalidArgumentException
     */
    public function setUpdatableMsg($activityId, $state = 0, $parameter = [])
    {
        if (!in_array($state, [0, 1])) {
            throw new InvalidArgumentException('"state" should be "0" or "1".');
        }

        $parameterList = $this->formatParameterList($parameter);

        $params = [
            'activity_id' => $activityId,
            'target_state' => $state,
            'template_info' => ['parameter_list' => $parameterList]
        ];
        return $this->httpPost('cgi-bin/message/wxopen/updatablemsg/send', $params);
    }

    /**
     * @param $data
     *
     * @return array
     */
    protected function formatParameterList($data)
    {
        $parameterList = [];

        foreach ($data as $name => $value) {
            if (!in_array($name, ['member_count', 'room_limit', 'path', 'version_type'])) {
                continue;
            }

            if ($name == 'version_type' && !in_array($value, ['develop', 'trial', 'release'])) {
                throw new InvalidArgumentException('Invalid value of attribute "version_type".');
            }

            $parameterList[] = [
                'name' => $name,
                'value' => strval($value)
            ];
        }

        return $parameterList;
    }
}
