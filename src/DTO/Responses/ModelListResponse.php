<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Responses;

use Lewenbraun\Ollama\DTO\Model\ModelDetails;
use Lewenbraun\Ollama\DTO\Model\ModelLocalData;

class ModelListResponse
{
    /**
     * @param array<ModelLocalData> $models
     */
    private function __construct(
        /** @var array<ModelLocalData> */
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
            static fn (array $modelData) => ModelLocalData::fromArray($modelData),
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
            static fn (ModelLocalData $model) => $model->toArray(),
            $this->models
        );

        return [
            'models' => $modelsArray,
        ];
    }
}
