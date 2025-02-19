<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Responses;

use Lewenbraun\Ollama\DTO\Model\ModelDetails;

class ModelListResponse
{
    /**
     * @param array<ModelDetails> $models
     */
    private function __construct(
        /** @var array<ModelDetails> */
        public readonly array $models
    ) {
    }

    /**
     * @param array $attributes
     * @return ModelListResponse
     */
    public static function fromArray(array $attributes): ModelListResponse
    {
        $modelsData = $attributes['models'] ?? [];
        $models = array_map(
            static fn (array $modelData) => ModelDetails::fromArray($modelData),
            $modelsData
        );

        return new self(
            models: $models,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $modelsArray = array_map(
            static fn (ModelDetails $model) => $model->toArray(),
            $this->models
        );

        return [
            'models' => $modelsArray,
        ];
    }
}
