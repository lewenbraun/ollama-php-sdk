<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama;

use Lewenbraun\Ollama\Client;
use Lewenbraun\Ollama\Resources\BlobsResource;
use Lewenbraun\Ollama\Resources\EmbedResource;
use Lewenbraun\Ollama\Resources\ModelResource;
use Lewenbraun\Ollama\Resources\VersionResource;
use Lewenbraun\Ollama\Resources\CompletionResource;
use Lewenbraun\Ollama\Resources\ChatCompletionResource;

final class Ollama
{
    private Client $client;

    public static function client(string $host = 'http://localhost:11434'): self
    {
        return new self(new Client($host));
    }

    private function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return CompletionResource
     */
    public function completion(): CompletionResource
    {
        return new CompletionResource($this->client);
    }

    /**
     * @return ChatCompletionResource
     */
    public function chatCompletion(): ChatCompletionResource
    {
        return new ChatCompletionResource($this->client);
    }

    /**
     * @return ModelResource
     */
    public function models(): ModelResource
    {
        return new ModelResource($this->client);
    }

    /**
     * @return BlobsResource
     */
    public function blobs(): BlobsResource
    {
        return new BlobsResource($this->client);
    }

    /**
     * @return EmbedResource
     */
    public function embed(): EmbedResource
    {
        return new EmbedResource($this->client);
    }

    /**
     * @return VersionResource
     */
    public function version(): VersionResource
    {
        return new VersionResource($this->client);
    }

    /**
     * @return boolean
     */
    public function isRunning(): bool
    {
        return $this->client->isRunning();
    }
}
