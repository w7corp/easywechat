<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;

/**
 * WeChat Work Customer Service Message Handler
 */
class KfMessage
{
    public function __construct(protected AccessTokenAwareClient $client)
    {
    }

    /**
     * Get session state.
     *
     * @param  string  $openKfId  Customer service ID
     * @param  string  $externalUserId  External user ID
     * @return array<string, mixed>
     */
    public function state(string $openKfId, string $externalUserId): array
    {
        return $this->client->postJson('/cgi-bin/kf/service_state/get', [
            'open_kfid' => $openKfId,
            'external_userid' => $externalUserId,
        ])->toArray();
    }

    /**
     * Update session state.
     *
     * @param  string  $openKfId  Customer service ID
     * @param  string  $externalUserId  External user ID
     * @param  int  $serviceState  Service state
     * @param  string  $serviceUserId  Service user ID
     * @return array<string, mixed>
     */
    public function updateState(string $openKfId, string $externalUserId, int $serviceState, string $serviceUserId): array
    {
        return $this->client->postJson('/cgi-bin/kf/service_state/trans', [
            'open_kfid' => $openKfId,
            'external_userid' => $externalUserId,
            'service_state' => $serviceState,
            'servicer_userid' => $serviceUserId,
        ])->toArray();
    }

    /**
     * Sync messages.
     *
     * @param  string  $cursor  Cursor for pagination
     * @param  string  $token  Token for syncing
     * @param  int  $limit  Maximum number of messages to retrieve
     * @param  string  $openKfId  Customer service ID (required parameter that was missing)
     * @return array<string, mixed>
     */
    public function sync(string $cursor, string $token, int $limit, string $openKfId): array
    {
        return $this->client->postJson('/cgi-bin/kf/sync_msg', [
            'cursor' => $cursor,
            'token' => $token,
            'limit' => $limit,
            'open_kfid' => $openKfId,
        ])->toArray();
    }

    /**
     * Send message.
     *
     * @param  array<string, mixed>  $params  Message parameters
     * @return array<string, mixed>
     */
    public function send(array $params): array
    {
        return $this->client->postJson('/cgi-bin/kf/send_msg', $params)->toArray();
    }

    /**
     * Send event response message.
     *
     * @param  array<string, mixed>  $params  Event parameters
     * @return array<string, mixed>
     */
    public function event(array $params): array
    {
        return $this->client->postJson('/cgi-bin/kf/send_msg_on_event', $params)->toArray();
    }
}
