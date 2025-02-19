<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Requests;

use Lewenbraun\Ollama\DTO\Request;

class EmbedRequest extends Request
{
    /**
     * @param string $model (required) name of model to generate embeddings from
     * @param string|array<string> $input (required) text or list of text to generate embeddings for
     * @param bool|null $truncate truncates the end of each input to fit within context length. Returns error if false and context length is exceeded. Defaults to true
     * @param array<string, mixed>|null $options additional model parameters listed in the documentation for the Modelfile such as temperature
     * @param string|null $keepAlive controls how long the model will stay loaded into memory following the request (default: 5m)
     */
    public function __construct(
        public readonly string $model,
        public readonly string|array $input,
        public readonly ?bool $truncate = null,
        public readonly ?array $options = null,
        public readonly ?string $keepAlive = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            model: $data['model'],
            input: $data['input'],
            truncate: $data['truncate'] ?? null,
            options: $data['options'] ?? null,
            keepAlive: $data['keep_alive'] ?? null,
        );
    }
}
