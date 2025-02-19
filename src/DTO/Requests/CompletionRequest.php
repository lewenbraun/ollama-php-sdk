<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Requests;

use Lewenbraun\Ollama\DTO\Request;

class CompletionRequest extends Request
{
    /**
     * @param string $model (required) the model name
     * @param string $prompt the prompt to generate a response for
     * @param bool $stream if false the response will be returned as a single response object, rather than a stream of objects
     * @param string|null $suffix the text after the model response
     * @param array<string>|null $images (optional) a list of base64-encoded images (for multimodal models such as llava)
     * @param string|null $format the format to return a response in. Format can be json or a JSON schema
     * @param array<string, mixed>|null $options additional model parameters listed in the documentation for the Modelfile such as temperature
     * @param string|null $system system message to (overrides what is defined in the Modelfile)
     * @param string|null $template the prompt template to use (overrides what is defined in the Modelfile)
     * @param bool|null $raw if true no formatting will be applied to the prompt. You may choose to use the raw parameter if you are specifying a full templated prompt in your request to the API
     * @param string|null $keepAlive controls how long the model will stay loaded into memory following the request (default: 5m)
     * @param array<mixed>|null $context (deprecated) the context parameter returned from a previous request to /generate, this can be used to keep a short conversational memory
     */
    public function __construct(
        public readonly string $model,
        public readonly ?string $prompt,
        public readonly ?bool $stream = false,
        public readonly ?string $suffix = null,
        public readonly ?array $images = null,
        public readonly ?string $format = null,
        public readonly ?array $options = null,
        public readonly ?string $system = null,
        public readonly ?string $template = null,
        public readonly ?bool $raw = null,
        public readonly ?string $keepAlive = null,
        public readonly ?array $context = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            model:  $data['model'],
            prompt: $data['prompt'] ?? false,
            stream: $data['stream'] ?? false,
            suffix: $data['suffix'] ?? null,
            images: $data['images'] ?? null,
            format: $data['format'] ?? null,
            options: $data['options'] ?? null,
            system: $data['system'] ?? null,
            template: $data['template'] ?? null,
            raw: $data['raw'] ?? null,
            keepAlive: $data['keep_alive'] ?? null,
            context: $data['context'] ?? null,
        );
    }
}
