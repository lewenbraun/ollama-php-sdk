<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Chat;

use Lewenbraun\Ollama\DTO\Chat\ToolCall;

class Message
{
    /**
     * @param string $role the role of the message, either system, user, assistant, or tool
     * @param string $content the content of the message
     * @param array<string>|null $images (optional) a list of images to include in the message (for multimodal models such as llava)
     * @param ToolCall[]|null $toolCalls (optional) a list of tools in JSON that the model wants to use
     */
    public function __construct(
        public readonly string $role,
        public readonly string $content,
        public readonly ?array $images = null,
        /** @var ToolCall[]|null */
        public readonly ?array $toolCalls = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $toolCalls = $data['tool_calls'] ?? null;

        if ($toolCalls !== null) {
            $toolCalls = array_map(
                fn (array $toolCallData) => ToolCall::fromArray($toolCallData),
                $toolCalls
            );
        }

        return new self(
            role: $data['role'],
            content: $data['content'],
            images: $data['images'] ?? null,
            toolCalls: $toolCalls,
        );
    }

    public function toArray(): array
    {
        $toolCallsArray = null;
        if ($this->toolCalls !== null) {
            $toolCallsArray = array_map(
                fn (ToolCall $toolCall) => $toolCall->toArray(),
                $this->toolCalls
            );
        }

        return [
            'role' => $this->role,
            'content' => $this->content,
            'tool_calls' => $toolCallsArray,
        ];
    }
}
