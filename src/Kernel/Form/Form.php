<?php

namespace EasyWeChat\Kernel\Form;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class Form
{
    /**
     * @param  array<string|array|DataPart>  $fields
     */
    public function __construct(protected array $fields)
    {
    }

    /**
     * @param  array<string|array|DataPart>  $fields
     */
    public static function create(array $fields): Form
    {
        return new self($fields);
    }

    /**
     * @return  array<string,mixed>
     */
    #[ArrayShape(['headers' => 'array', 'body' => 'string'])]
    public function toArray(): array
    {
        return $this->toOptions();
    }

    /**
     * @return array<string,mixed>
     */
    #[ArrayShape(['headers' => 'array', 'body' => 'string'])]
    public function toOptions(): array
    {
        $formData = new FormDataPart($this->fields);

        return [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToString(),
        ];
    }
}
