<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Resources;

use Lewenbraun\Ollama\Client;
use Lewenbraun\Ollama\Contracts\BlobsResourceContract;
use Lewenbraun\Ollama\DTO\Requests\BlobExistsRequest;
use Lewenbraun\Ollama\DTO\Requests\BlobPushRequest;

final class BlobsResource implements BlobsResourceContract
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
     * @return bool
     */
    public function exists(array $parameters): bool
    {
        $blobExistsRequest = BlobExistsRequest::fromArray($parameters);
        $digest = $blobExistsRequest->digest;

        $statusCode = $this->client->handle('head', "blobs/{$digest}");

        return $statusCode === 200 ? true : false;
    }

    /**
     * @param array $parameters
     * @return bool
     */
    public function push(array $parameters): bool
    {
        $blobPushRequest = BlobPushRequest::fromArray($parameters);
        $digest = $blobPushRequest->digest;

        $statusCode = $this->client->handle('post', "blobs/{$digest}", $blobPushRequest);

        return $statusCode === 201 ? true : false;
    }
}
