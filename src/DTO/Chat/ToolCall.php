<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Chat;

use Lewenbraun\Ollama\DTO\Chat\FunctionCall;

class ToolCall
{
    /**
     * @param FunctionCall $function
     */
    public function __construct(
        public readonly FunctionCall $function,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            function: FunctionCall::fromArray($data['function']),
        );
    }

    public function toArray(): array
    {
        return [
            'function' => $this->function->toArray(),
        ];
    }
}
