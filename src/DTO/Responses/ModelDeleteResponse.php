<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Requests;

use Lewenbraun\Ollama\DTO\Request;

class ModelPullRequest extends Request
{
    /**
     * @param string $model (required) name of the model to pull
     * @param bool|null $insecure (optional) allow insecure connections to the library
     * @param bool|null $stream (optional) if false the response will be returned as a single response object, rather than a stream of objects
     */
    public function __construct(
        public readonly string $model,
        public readonly ?bool $insecure = null,
        public readonly ?bool $stream = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            model: $data['model'],
            insecure: $data['insecure'] ?? null,
            stream: $data['stream'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'insecure' => $this->insecure,
            'stream' => $this->stream,
        ];
    }
}
