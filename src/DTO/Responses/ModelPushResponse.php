<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Responses;

class ModelPushResponse
{
    /**
     * @param string|null $status
     * @param string|null $digest
     * @param int|null $total
     * @param int|null $completed
     */
    private function __construct(
        public readonly ?string $status = null,
        public readonly ?string $digest = null,
        public readonly ?int $total = null,
        public readonly ?int $completed = null,
    ) {
    }

    /**
     * @param array $attributes
     * @return ModelPushResponse
     */
    public static function fromArray(array $attributes): ModelPushResponse
    {
        return new self(
            status: $attributes['status'] ?? null,
            digest: $attributes['digest'] ?? null,
            total: $attributes['total'] ?? null,
            completed: $attributes['completed'] ?? null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'digest' => $this->digest,
            'total' => $this->total,
            'completed' => $this->completed,
        ];
    }
}
