<?php

namespace EasyWeChat\Kernel\Form;

use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class Form
{
    public function __construct(protected array $fields)
    {
    }

    public static function create(array $fields): Form
    {
        return new self($fields);
    }

    public function toArray(): array
    {
        return $this->toOptions();
    }

    public function toOptions(): array
    {
        $formData = new FormDataPart($this->fields);

        return [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToString(),
        ];
    }
}
