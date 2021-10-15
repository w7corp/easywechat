<?php

namespace EasyWeChat\Pay\V3;

class Native extends BaseProvideV3
{
    public function pay(
        string $appid,
        string $description,
        string $out_trade_no,
        string $notify_url,
        int $total,
        string $currency = "CNY",
        array $other = []
    ): array {
        $data = [
            'appid' => $appid,
            'mchid' => $this->getMerchantId(),
            'description' => $description,
            'out_trade_no' => $out_trade_no,
            'notify_url' => $notify_url,
            'amount' => [
                'total' => $total,
                'currency' => $currency
            ]
        ];

        $data = array_merge($data, $other);

        $response = $this->client->post("pay/transactions/native", [
            'json' => $data
        ]);

        return $this->getResult($response);
    }

    public function id(string $transaction_id)
    {
        $mch_id = $this->getMerchantId();

        $response = $this->client->get("pay/transactions/id/".$transaction_id."?mchid=".$mch_id);

        return $this->getResult($response);
    }
}
