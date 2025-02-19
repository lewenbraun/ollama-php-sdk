<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Resources;

use Lewenbraun\Ollama\Client;
use Lewenbraun\Ollama\Adapters\StreamWrapper;
use Lewenbraun\Ollama\DTO\Requests\CompletionRequest;
use Lewenbraun\Ollama\DTO\Responses\CompletionResponse;
use Lewenbraun\Ollama\Contracts\CompletionResourceContract;

final class CompletionResource implements CompletionResourceContract
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
     * @return CompletionResponse|StreamWrapper
     */
    public function create(array $parameters): CompletionResponse|StreamWrapper
    {
        $completionRequest = CompletionRequest::fromArray($parameters);

        $response = $this->client->handle('post', 'generate', $completionRequest);

        if ($completionRequest->stream) {
            return new StreamWrapper(CompletionResponse::class, $response);
        }

        return CompletionResponse::fromArray($response);
    }
}
