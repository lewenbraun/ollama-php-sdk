<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Requests;

use Lewenbraun\Ollama\DTO\Request;

class ModelDeleteRequest extends Request
{
    /**
     * @param string $model (required) model name to delete
     */
    public function __construct(
        public readonly string $model
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            model: $data['model'],
        );
    }
}
