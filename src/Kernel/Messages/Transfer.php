<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $to
 * @property string $account
 */
class Transfer extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected string $type = 'transfer_customer_service';

    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = [
        'account',
    ];

    /**
     * @param string|null $account
     */
    public function __construct(string $account = null)
    {
        parent::__construct(compact('account'));
    }

    /**
     * @return array
     */
    public function toXmlArray(): array
    {
        return empty($this->get('account')) ? [] : [
            'TransInfo' => [
                'KfAccount' => $this->get('account'),
            ],
        ];
    }
}
