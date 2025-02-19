<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Requests;

use Lewenbraun\Ollama\DTO\Request;
use Lewenbraun\Ollama\DTO\Chat\Message;

class ChatCompletionRequest extends Request
{
    /**
     * @param string $model (required) the model name
     * @param array<Message> $messages (required) the messages of the chat, this can be used to keep a chat memory
     * @param array<mixed>|null $tools list of tools in JSON for the model to use if supported
     * @param string|null $format the format to return a response in. Format can be json or a JSON schema.
     * @param array<string, mixed>|null $options additional model parameters listed in the documentation for the Modelfile such as temperature
     * @param bool $stream if false the response will be returned as a single response object, rather than a stream of objects
     * @param string|null $keepAlive controls how long the model will stay loaded into memory following the request (default: 5m)
     */
    public function __construct(
        public readonly string $model,
        /** @var array<Message> */
        public readonly ?array $messages,
        public readonly ?array $tools = null,
        public readonly ?string $format = null,
        public readonly ?array $options = null,
        public readonly ?bool $stream = false,
        public readonly ?string $keepAlive = null,
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
            model:  $data['model'],
            messages: $messages  ?? null,
            tools: $data['tools'] ?? null,
            format: $data['format'] ?? null,
            options: $data['options'] ?? null,
            stream: $data['stream'] ?? false,
            keepAlive: $data['keep_alive'] ?? null,
        );
    }
}
