<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Responses;

class ModelCreateResponse
{
    /**
     * @param string $status
     */
    private function __construct(
        public readonly string $status,
    ) {
    }

    /**
     * @param array $attributes
     * @return ModelCreateResponse
     */
    public static function fromArray(array $attributes): ModelCreateResponse
    {
        return new self(
            status: $attributes['status'],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
        ];
    }
}
