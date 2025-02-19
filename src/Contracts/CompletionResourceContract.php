<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Contracts;

use Lewenbraun\Ollama\Adapters\StreamWrapper;
use Lewenbraun\Ollama\DTO\Responses\CompletionResponse;

interface CompletionResourceContract
{
    /**
     * Generate a response for a given prompt with a provided model
     *
     * @param array $parameters
     * @return CompletionResponse|StreamWrapper
     */
    public function create(array $parameters): CompletionResponse|StreamWrapper;
}
