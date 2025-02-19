<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Requests;

use Lewenbraun\Ollama\DTO\Request;

class ModelCopyRequest extends Request
{
    /**
     * @param string $source (required) name of the source model
     * @param string $destination (required) name of the destination model
     */
    public function __construct(
        public readonly string $source,
        public readonly string $destination
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            source: $data['source'],
            destination: $data['destination'],
        );
    }
}
