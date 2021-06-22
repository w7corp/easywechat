<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Kernel\Traits\HasAttributes;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ServerRequestInterface;

abstract class Message
{
    use HasAttributes;

    public function __construct(
        array $attributes = [],
        protected ?string $originContent = ''
    ) {
        $this->attributes = $attributes;
    }

    public function getOriginalContents(): ?string
    {
        return $this->originContent;
    }

    #[Pure]
    public function __toString()
    {
        return $this->getOriginalContents();
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public static function format(string $originContent): array
    {
        if (0 === stripos($originContent, '<')) {
            $attributes = Xml::parse($originContent);
        }

        // Handle JSON format.
        $dataSet = json_decode($originContent, true);

        if (JSON_ERROR_NONE === json_last_error() && $originContent) {
            $attributes = $dataSet;
        }

        if (empty($attributes)) {
            throw new BadRequestException('Failed to decode request contents.');
        }

        return $attributes;
    }

    abstract static function createFromRequest(ServerRequestInterface $request, ?Encryptor $encryptor = null): static;
}
