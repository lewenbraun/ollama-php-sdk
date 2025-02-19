<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Model;

class ModelRunningData
{
    /**
     * @param string $name
     * @param string $model
     * @param int $size
     * @param string $digest
     * @param ModelDetails $details
     * @param string $expiresAt
     * @param int $sizeVram
     */
    private function __construct(
        public readonly string $name,
        public readonly string $model,
        public readonly int $size,
        public readonly string $digest,
        public readonly ModelDetails $details,
        public readonly string $expiresAt,
        public readonly int $sizeVram,
    ) {
    }

    public static function fromArray(array $attributes): ModelRunningData
    {
        return new self(
            name: $attributes['name'],
            model: $attributes['model'],
            size: $attributes['size'],
            digest: $attributes['digest'],
            details: ModelDetails::fromArray($attributes['details']),
            expiresAt: $attributes['expires_at'],
            sizeVram: $attributes['size_vram'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'model' => $this->model,
            'size' => $this->size,
            'digest' => $this->digest,
            'details' => $this->details->toArray(),
            'expires_at' => $this->expiresAt,
            'size_vram' => $this->sizeVram,
        ];
    }
}
