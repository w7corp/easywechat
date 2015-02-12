<?php

namespace Overtrue\Wechat\Services;

class QRCode
{
    const API_CREATE = 'https://mp.weixin.qq.com/cgi-bin/qrcode/create';
    const API_SHOW   = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';

    public function create($sceneId, $temporary = true)
    {
        $params = array(
                   'expire_seconds' => 1800,
                   'action_name'    => $temporary ? 'QR_SCENE' : 'QR_LIMIT_SCENE',
                   'action_info'    => array(
                                        'scene' => array(
                                                    'scene_id' => intval($sceneId),
                                                   ),
                                       ),
                  );
        //TODO
    }

    public function show()
    {
        # code...
    }
}