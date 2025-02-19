<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Resources;

use Lewenbraun\Ollama\Client;
use Lewenbraun\Ollama\Contracts\EmbedResourceContract;
use Lewenbraun\Ollama\DTO\Requests\EmbedRequest;
use Lewenbraun\Ollama\DTO\Responses\EmbedResponse;

final class EmbedResource implements EmbedResourceContract
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
     * @param array $parameters
     * @return EmbedResponse
     */
    public function create(array $parameters): EmbedResponse
    {
        $embedRequest = EmbedRequest::fromArray($parameters);

        $response = $this->client->handle('post', 'embed', $embedRequest);

        return EmbedResponse::fromArray($response);
    }
}
