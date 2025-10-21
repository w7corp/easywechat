<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;

/**
 * WeChat Work Customer Service Account Handler
 */
class KfAccount
{
    public function __construct(protected AccessTokenAwareClient $client)
    {
    }

    /**
     * Add customer service account.
     *
     * @param  string  $name  Account name
     * @param  string  $mediaId  Avatar media ID
     * @return array<string, mixed>
     */
    public function add(string $name, string $mediaId): array
    {
        return $this->client->postJson('/cgi-bin/kf/account/add', [
            'name' => $name,
            'media_id' => $mediaId,
        ])->toArray();
    }

    /**
     * Delete customer service account.
     *
     * @param  string  $openKfId  Customer service ID
     * @return array<string, mixed>
     */
    public function del(string $openKfId): array
    {
        return $this->client->postJson('/cgi-bin/kf/account/del', [
            'open_kfid' => $openKfId,
        ])->toArray();
    }

    /**
     * Update customer service account.
     *
     * @param  string  $openKfId  Customer service ID
     * @param  string  $name  Account name
     * @param  string  $mediaId  Avatar media ID
     * @return array<string, mixed>
     */
    public function update(string $openKfId, string $name, string $mediaId): array
    {
        return $this->client->postJson('/cgi-bin/kf/account/update', [
            'open_kfid' => $openKfId,
            'name' => $name,
            'media_id' => $mediaId,
        ])->toArray();
    }

    /**
     * Get customer service account list.
     *
     * @return array<string, mixed>
     */
    public function list(): array
    {
        return $this->client->postJson('/cgi-bin/kf/account/list')->toArray();
    }

    /**
     * Get customer service account link.
     *
     * @param  string  $openKfId  Customer service ID
     * @param  string  $scene  Scene value
     * @return array<string, mixed>
     */
    public function getAccountLink(string $openKfId, string $scene): array
    {
        return $this->client->postJson('/cgi-bin/kf/add_contact_way', [
            'open_kfid' => $openKfId,
            'scene' => $scene,
        ])->toArray();
    }
}
