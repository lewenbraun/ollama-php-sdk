<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Model;

class ModelDetails
{
    /**
     * @param string $parentModel
     * @param string $format
     * @param string $family
     * @param array<string> $families
     * @param string $parameterSize
     * @param string $quantizationLevel
     */
    private function __construct(
        public readonly string $parentModel,
        public readonly string $format,
        public readonly string $family,
        public readonly array $families,
        public readonly string $parameterSize,
        public readonly string $quantizationLevel,
    ) {
    }

    public static function fromArray(array $attributes): ModelDetails
    {
        return new self(
            parentModel: $attributes['parent_model'],
            format: $attributes['format'],
            family: $attributes['family'],
            families: $attributes['families'],
            parameterSize: $attributes['parameter_size'],
            quantizationLevel: $attributes['quantization_level'],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'parent_model' => $this->parentModel,
            'format' => $this->format,
            'family' => $this->family,
            'families' => $this->families,
            'parameter_size' => $this->parameterSize,
            'quantization_level' => $this->quantizationLevel,
        ];
    }
}
