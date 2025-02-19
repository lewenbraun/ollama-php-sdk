<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Resources;

use Lewenbraun\Ollama\Client;
use Lewenbraun\Ollama\DTO\Requests\ChatCompletionRequest;
use Lewenbraun\Ollama\Adapters\StreamWrapper;
use Lewenbraun\Ollama\DTO\Responses\ChatCompletionResponse;
use Lewenbraun\Ollama\Contracts\ChatCompletionResourceContract;

class ChatCompletionResource implements ChatCompletionResourceContract
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
     * @return ChatCompletionResponse|StreamWrapper
     */
    public function create(array $parameters): ChatCompletionResponse|StreamWrapper
    {
        $chatCompletionRequest = ChatCompletionRequest::fromArray($parameters);
        $response = $this->client->handle('post', 'chat', $chatCompletionRequest);

        if ($chatCompletionRequest->stream) {
            return new StreamWrapper(ChatCompletionResponse::class, $response);
        }

        return ChatCompletionResponse::fromArray($response);
    }
}
