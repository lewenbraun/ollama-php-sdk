<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Requests;

use Lewenbraun\Ollama\DTO\Request;
use Lewenbraun\Ollama\DTO\Chat\Message;

class ModelCreateRequest extends Request
{
    /**
     * @param string $model (required) name of the model to create
     * @param string|null $from (optional) name of an existing model to create the new model from
     * @param array<string, string>|null $files (optional) a dictionary of file names to SHA256 digests of blobs to create the model from
     * @param array<string, string>|null $adapters (optional) a dictionary of file names to SHA256 digests of blobs for LORA adapters
     * @param string|null $template (optional) the prompt template for the model
     * @param string|array|null $license (optional) a string or list of strings containing the license or licenses for the model
     * @param string|null $system (optional) a string containing the system prompt for the model
     * @param array<string, mixed>|null $parameters (optional) a dictionary of parameters for the model (see Modelfile for a list of parameters)
     * @param array<Message>|null $messages (optional) a list of message objects used to create a conversation
     * @param bool|null $stream (optional) if false the response will be returned as a single response object, rather than a stream of objects
     * @param string|null $quantize (optional) quantize a non-quantized (e.g. float16) model
     */
    public function __construct(
        public readonly string $model,
        public readonly ?string $from = null,
        /** @var array<string, string>|null */
        public readonly ?array $files = null,
        /** @var array<string, string>|null */
        public readonly ?array $adapters = null,
        public readonly ?string $template = null,
        public readonly string|array|null $license = null,
        public readonly ?string $system = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $parameters = null,
        /** @var array<Message>|null */
        public readonly ?array $messages = null,
        public readonly ?bool $stream = false,
        public readonly ?string $quantize = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $messagesData = $data['messages'] ?? [];
        $messages = array_map(
            static fn (array $messageData) => Message::fromArray($messageData),
            $messagesData
        );

        return new self(
            model: $data['model'],
            from: $data['from'] ?? null,
            files: $data['files'] ?? null,
            adapters: $data['adapters'] ?? null,
            template: $data['template'] ?? null,
            license: $data['license'] ?? null,
            system: $data['system'] ?? null,
            parameters: $data['parameters'] ?? null,
            messages: $messages ?? null,
            stream: $data['stream'] ?? false,
            quantize: $data['quantize'] ?? null,
        );
    }
}
