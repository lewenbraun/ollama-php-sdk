<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Responses;

class VersionResponse
{
    /**
     * @param string $version
     */
    private function __construct(
        public readonly string $version,
    ) {
    }

    /**
     * @param array $attributes
     * @return VersionResponse
     */
    public static function fromArray(array $attributes): VersionResponse
    {
        return new self(
            version: $attributes['version'],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'version' => $this->version,
        ];
    }
}
