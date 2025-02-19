<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Contracts;

use Lewenbraun\Ollama\DTO\Responses\VersionResponse;

interface VersionResourceContract
{
    /**
     * Retrieve the Ollama version.
     *
     * @return VersionResponse
     */
    public function show(): VersionResponse;
}
