<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Responses;

class EmbedResponse
{
    /**
     * @param string $model
     * @param array<array<float>> $embeddings
     * @param int|null $totalDuration
     * @param int|null $loadDuration
     * @param int|null $promptEvalCount
     */
    private function __construct(
        public readonly string $model,
        /** @var array<array<float>> */
        public readonly array $embeddings,
        public readonly ?int $totalDuration = null,
        public readonly ?int $loadDuration = null,
        public readonly ?int $promptEvalCount = null,
    ) {
    }

    /**
     * @param array $attributes
     * @return EmbedResponse
     */
    public static function fromArray(array $attributes): EmbedResponse
    {
        return new self(
            model: $attributes['model'],
            embeddings: $attributes['embeddings'],
            totalDuration: $attributes['total_duration'] ?? null,
            loadDuration: $attributes['load_duration'] ?? null,
            promptEvalCount: $attributes['prompt_eval_count'] ?? null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'embeddings' => $this->embeddings,
            'total_duration' => $this->totalDuration,
            'load_duration' => $this->loadDuration,
            'prompt_eval_count' => $this->promptEvalCount,
        ];
    }
}
