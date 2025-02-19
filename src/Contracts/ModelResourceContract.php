<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Contracts;

use Lewenbraun\Ollama\DTO\Responses\ModelCopyResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelListResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelPullResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelPushResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelShowResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelCreateResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelDeleteResponse;
use Lewenbraun\Ollama\Adapters\StreamWrapper;
use Lewenbraun\Ollama\DTO\Responses\ModelRunningListResponse;

interface ModelResourceContract
{
    /**
     * Create a model from:
     * - another model;
     * - a safetensors directory; or
     * - a GGUF file.
     *
     * @param array $parameters
     * @return ModelCreateResponse|StreamWrapper
     */
    public function create(array $parameters): ModelCreateResponse|StreamWrapper;

    /**
     * List models that are available locally.
     *
     * @return ModelListResponse
     */
    public function list(): ModelListResponse;

    /**
    * List models that are currently loaded into memory.
    *
    * @return ModelRunningListResponse
    */
    public function listRunning(): ModelRunningListResponse;

    /**
     * Show information about a model including details, modelfile, template, parameters, license, system prompt.
     *
     * @param array $parameters
     * @return ModelShowResponse
     */
    public function show(array $parameters): ModelShowResponse;

    /**
     * Copy a model. Creates a model with another name from an existing model.
     *
     * @param array $parameters
     */
    public function copy(array $parameters): bool;

    /**
     * Delete a model and its data.
     *
     * @param array $parameters
     */
    public function delete(array $parameters): bool;

    /**
     * Download a model from the ollama library.
     * Cancelled pulls are resumed from where they left off, and multiple calls will share the same download progress.
     *
     * @param array $parameters
     * @return ModelPullResponse|StreamWrapper
     */
    public function pull(array $parameters): ModelPullResponse|StreamWrapper;

    /**
     * Upload a model to a model library. Requires registering for ollama.ai and adding a public key first.
     *
     * @param array $parameters
     * @return ModelPushResponse|StreamWrapper
     */
    public function push(array $parameters): ModelPushResponse|StreamWrapper;
}
