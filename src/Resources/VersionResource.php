<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Resources;

use Lewenbraun\Ollama\Client;
use Lewenbraun\Ollama\Contracts\VersionResourceContract;
use Lewenbraun\Ollama\DTO\Responses\VersionResponse;

final class VersionResource implements VersionResourceContract
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return VersionResponse
     */
    public function show(): VersionResponse
    {
        $response = $this->client->handle('get', 'version');

        return VersionResponse::fromArray($response);
    }
}
