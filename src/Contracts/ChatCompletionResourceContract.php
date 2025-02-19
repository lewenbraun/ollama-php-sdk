<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Contracts;

use Lewenbraun\Ollama\Adapters\StreamWrapper;
use Lewenbraun\Ollama\DTO\Responses\ChatCompletionResponse;

interface ChatCompletionResourceContract
{
    /**
     * Generate the next message in a chat with a provided model
     *
     * @param array $parameters
     * @return ChatCompletionResponse|StreamWrapper
     */
    public function create(array $parameters): ChatCompletionResponse|StreamWrapper;
}
