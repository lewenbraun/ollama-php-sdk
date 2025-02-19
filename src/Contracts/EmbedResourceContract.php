<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Contracts;

use Lewenbraun\Ollama\DTO\Responses\EmbedResponse;

interface EmbedResourceContract
{
    /**
     * Generate embeddings from a model.
     *
     * @param array $parameters
     * @return EmbedResponse
     */
    public function create(array $parameters): EmbedResponse;
}
