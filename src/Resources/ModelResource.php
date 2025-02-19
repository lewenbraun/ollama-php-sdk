<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Resources;

use Lewenbraun\Ollama\Client;
use Lewenbraun\Ollama\DTO\Requests\ModelCopyRequest;
use Lewenbraun\Ollama\DTO\Requests\ModelPullRequest;
use Lewenbraun\Ollama\DTO\Requests\ModelPushRequest;
use Lewenbraun\Ollama\DTO\Requests\ModelShowRequest;
use Lewenbraun\Ollama\Contracts\ModelResourceContract;
use Lewenbraun\Ollama\DTO\Requests\ModelCreateRequest;
use Lewenbraun\Ollama\DTO\Requests\ModelDeleteRequest;
use Lewenbraun\Ollama\DTO\Responses\ModelListResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelPullResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelPushResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelShowResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelCreateResponse;
use Lewenbraun\Ollama\Adapters\StreamWrapper;
use Lewenbraun\Ollama\DTO\Responses\ModelRunningListResponse;

final class ModelResource implements ModelResourceContract
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
     * @return ModelCreateResponse|StreamWrapper
     */
    public function create(array $parameters): ModelCreateResponse|StreamWrapper
    {
        $modelCreateRequest = ModelCreateRequest::fromArray($parameters);

        $response = $this->client->handle('post', 'create', $modelCreateRequest);

        if ($modelCreateRequest->stream) {
            return new StreamWrapper(ModelCreateResponse::class, $response);
        }

        return ModelCreateResponse::fromArray($response);
    }

    /**
     * @return ModelListResponse
     */
    public function list(): ModelListResponse
    {
        $response = $this->client->handle('get', 'tags');

        return ModelListResponse::fromArray($response);
    }

    /**
     * @return ModelRunningListResponse
     */
    public function listRunning(): ModelRunningListResponse
    {
        $response = $this->client->handle('get', 'ps');

        return ModelRunningListResponse::fromArray($response);
    }

    /**
     * @param array $parameters
     * @return ModelShowResponse
     */
    public function show(array $parameters): ModelShowResponse
    {
        $modelShowRequest = ModelShowRequest::fromArray($parameters);

        $response = $this->client->handle('post', 'show', $modelShowRequest);

        return ModelShowResponse::fromArray($response);
    }

    /**
     * @param array $parameters
     * @return boolean
     */
    public function copy(array $parameters): bool
    {
        $modelCopyRequest = ModelCopyRequest::fromArray($parameters);

        $statusCode = $this->client->handle('post', 'copy', $modelCopyRequest);

        return $statusCode === 200 ? true : false;
    }

    /**
     * @param array $parameters
     * @return boolean
     */
    public function delete(array $parameters): bool
    {
        $modelDeleteRequest = ModelDeleteRequest::fromArray($parameters);

        $statusCode = $this->client->handle('delete', 'delete', $modelDeleteRequest);

        return $statusCode === 200 ? true : false;
    }

    /**
     * @param array $parameters
     * @return ModelPullResponse|StreamWrapper
     */
    public function pull(array $parameters): ModelPullResponse|StreamWrapper
    {
        $modelPullRequest = ModelPullRequest::fromArray($parameters);

        $response = $this->client->handle('post', 'pull', $modelPullRequest);

        if ($modelPullRequest->stream) {
            return new StreamWrapper(ModelPullResponse::class, $response);
        }

        return ModelPullResponse::fromArray($response);
    }

    /**
     * @param array $parameters
     * @return ModelPushResponse|StreamWrapper
     */
    public function push(array $parameters): ModelPushResponse|StreamWrapper
    {
        $modelPushRequest = ModelPushRequest::fromArray($parameters);

        $response = $this->client->handle('post', 'push', $modelPushRequest);

        if ($modelPushRequest->stream) {
            return new StreamWrapper(ModelPushResponse::class, $response);
        }

        return ModelPushResponse::fromArray($response);
    }
}
