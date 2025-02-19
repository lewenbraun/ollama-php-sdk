<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Requests;

use Lewenbraun\Ollama\DTO\Request;

class ModelShowRequest extends Request
{
    /**
     * @param string $model (required) name of the model to show
     * @param bool|null $verbose (optional) if set to true, returns full data for verbose response fields
     */
    public function __construct(
        public readonly string $model,
        public readonly ?bool $verbose = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            model: $data['model'],
            verbose: $data['verbose'] ?? null,
        );
    }
}
