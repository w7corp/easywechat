<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;

/**
 * WeChat Work Customer Service Servicer Handler
 */
class KfServicer
{
    public function __construct(protected AccessTokenAwareClient $client)
    {
    }

    /**
     * Add servicers to customer service account.
     *
     * @param  string  $openKfId  Customer service ID
     * @param  array<string>  $userIds  User IDs to add
     * @return array<string, mixed>
     */
    public function add(string $openKfId, array $userIds): array
    {
        return $this->client->postJson('/cgi-bin/kf/servicer/add', [
            'open_kfid' => $openKfId,
            'userid_list' => $userIds,
        ])->toArray();
    }

    /**
     * Delete servicers from customer service account.
     *
     * @param  string  $openKfId  Customer service ID
     * @param  array<string>  $userIds  User IDs to delete
     * @return array<string, mixed>
     */
    public function del(string $openKfId, array $userIds): array
    {
        return $this->client->postJson('/cgi-bin/kf/servicer/del', [
            'open_kfid' => $openKfId,
            'userid_list' => $userIds,
        ])->toArray();
    }

    /**
     * Get servicer list of customer service account.
     *
     * @param  string  $openKfId  Customer service ID
     * @return array<string, mixed>
     */
    public function list(string $openKfId): array
    {
        return $this->client->get('/cgi-bin/kf/servicer/list', [
            'query' => [
                'open_kfid' => $openKfId,
            ],
        ])->toArray();
    }
}
