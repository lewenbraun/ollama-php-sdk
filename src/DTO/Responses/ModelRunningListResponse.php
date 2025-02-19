<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Responses;

use Lewenbraun\Ollama\DTO\Model\ModelRunningData;

class ModelRunningListResponse
{
    /**
     * @param array<ModelRunningData> $models
     */
    private function __construct(
        /** @var array<ModelRunningData> */
        public readonly array $models,
    ) {
    }

    /**
     * @param array $attributes
     * @return ModelRunningListResponse
     */
    public static function fromArray(array $attributes): ModelRunningListResponse
    {
        $modelsData = $attributes['models'] ?? [];
        $models = array_map(
            static fn (array $modelData) => ModelRunningData::fromArray($modelData),
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
        return [
            'models' => array_map(
                static fn (ModelRunningData $model) => $model->toArray(),
                $this->models
            ),
        ];
    }
}
