<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Contracts;

use Lewenbraun\Ollama\DTO\Responses\BlobExistsResponse;
use Lewenbraun\Ollama\DTO\Responses\BlobPushResponse;

interface BlobsResourceContract
{
    /**
     * Ensures that the file blob used with create a model exists on the server
     *
     * @param array $parameters
     * @return bool
     */
    public function exists(array $parameters): bool;

    /**
     * Push a file to the Ollama server to create a "blob"
     *
     * @param array $parameters
     * @return bool
     */
    public function push(array $parameters): bool;
}
