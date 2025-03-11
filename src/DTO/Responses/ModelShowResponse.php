<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Responses;

use Lewenbraun\Ollama\DTO\Model\ModelDetails;

class ModelShowResponse
{
    /**
     * @param string $modelfile
     * @param string $parameters
     * @param string $template
     * @param ModelDetails $details
     * @param array<string, mixed> $model_info
     */
    private function __construct(
        public readonly string $modelfile,
        public readonly string $template,
        public readonly ModelDetails $details,
        public readonly array $model_info,
        public readonly ?string $parameters = null,
    ) {
    }

    public static function fromArray(array $attributes): ModelShowResponse
    {
        return new self(
            modelfile: $attributes['modelfile'],
            template: $attributes['template'],
            details: ModelDetails::fromArray($attributes['details']),
            model_info: $attributes['model_info'],
            parameters: $attributes['parameters'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'modelfile' => $this->modelfile,
            'parameters' => $this->parameters,
            'template' => $this->template,
            'details' => $this->details->toArray(),
            'model_info' => $this->model_info,
        ];
    }
}
