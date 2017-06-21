<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OfficialAccount\Menu;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * Get all menus.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->httpGet('cgi-bin/menu/get');
    }

    /**
     * Get current menus.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->httpGet('cgi-bin/get_current_selfmenu_info');
    }

    /**
     * Add menu.
     *
     * @param array $buttons
     * @param array $matchRule
     *
     * @return mixed
     */
    public function add(array $buttons, array $matchRule = [])
    {
        if (!empty($matchRule)) {
            return $this->httpPostJson('cgi-bin/menu/addconditional', [
                'button' => $buttons,
                'matchrule' => $matchRule,
            ]);
        }

        return $this->httpPostJson('cgi-bin/menu/create', ['button' => $buttons]);
    }

    /**
     * Destroy menu.
     *
     * @param int $menuId
     *
     * @return mixed
     */
    public function destroy($menuId = null)
    {
        if (!is_null($menuId)) {
            return $this->httpPostJson('cgi-bin/menu/delconditional', ['menuid' => $menuId]);
        }

        return $this->httpGet('cgi-bin/menu/delete');
    }

    /**
     * Test conditional menu.
     *
     * @param string $userId
     *
     * @return mixed
     */
    public function test($userId)
    {
        return $this->httpPostJson('cgi-bin/menu/trymatch', ['user_id' => $userId]);
    }
}
