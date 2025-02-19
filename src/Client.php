<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;
use Lewenbraun\Ollama\Exceptions\OllamaException;

class Client
{
    /**
     * Guzzle HTTP client instance.
     *
     * @var GuzzleClient
     */
    private GuzzleClient $guzzleClient;

    /**
     * @param string $host The base host URL for the API. Default is 'http://localhost:11434/'.
     */
    public function __construct(
        private readonly string $host = 'http://localhost:11434/',
    ) {
        // Initialize Guzzle client with the API base URI.
        $this->guzzleClient = new GuzzleClient([
            'base_uri' => "$host/api/",
        ]);
    }

    /**
     * Handles an HTTP request.
     *
     * Depending on the provided HTTP method, this method sends the request
     * and returns either the raw response, the HTTP status code, or a decoded JSON array.
     *
     * @param string $httpMethod
     * @param string $endpoint
     * @param object|null $request Optional request parameters object.
     *
     * @return ResponseInterface|int|array
     *
     * @throws OllamaException If a client, server, or unexpected error occurs.
     */
    public function handle(string $httpMethod, string $endpoint, ?object $request = null): ResponseInterface|int|array
    {
        try {
            $clientResponse = match ($httpMethod) {
                'get'    => $this->get($endpoint, $request),
                'post'   => $this->post($endpoint, $request),
                'head'   => $this->head($endpoint),
                'delete' => $this->delete($endpoint, $request),
                default  => throw new \InvalidArgumentException("Unsupported HTTP method: {$httpMethod}"),
            };

            // Process the client response based on the request details.
            $handleResponse = $this->getHandleResponse($httpMethod, $request, $clientResponse);

            return $handleResponse;
        } catch (ClientException $e) {
            throw $this->createOllamaException($e);
        } catch (ServerException $e) {
            throw $this->createOllamaException($e);
        } catch (\Exception $e) {
            throw new OllamaException('Unexpected error: ' . $e->getMessage(), 0);
        }
    }

    /**
     * Processes the client response based on the HTTP method and request options.
     *
     * - If streaming is requested, the raw response is returned.
     * - For HEAD or DELETE methods (or a successful response with an empty body), only the status code is returned.
     * - Otherwise, the response body is decoded from JSON as an array.
     *
     * @param string $httpMethod         The HTTP method used.
     * @param object|null $request       The request parameters object.
     * @param ResponseInterface $clientResponse The response from the HTTP client.
     *
     * @return ResponseInterface|int|array
     */
    private function getHandleResponse(string $httpMethod, ?object $request, ResponseInterface $clientResponse): ResponseInterface|int|array
    {
        if ($request?->stream) {
            return $clientResponse;
        }

        if ($this->shouldReturnStatusCodeOnly($httpMethod, $clientResponse, $request)) {
            return $clientResponse->getStatusCode();
        }

        return json_decode((string)$clientResponse->getBody(), true);
    }

    /**
     * Determines whether to return only the HTTP status code.
     *
     * This is true for HEAD or DELETE methods, or if the response is successful (2xx)
     * and the body is empty.
     *
     * @param string $httpMethod         The HTTP method used.
     * @param ResponseInterface $clientResponse The response from the HTTP client.
     * @param object|null $request       The request parameters object.
     *
     * @return bool True if only the status code should be returned, false otherwise.
     */
    private function shouldReturnStatusCodeOnly(string $httpMethod, ResponseInterface $clientResponse, ?object $request): bool
    {
        $statusCode = $clientResponse->getStatusCode();
        $body = (string)$clientResponse->getBody();

        $checkMethods = $httpMethod === 'head' || $httpMethod === 'delete';
        $checkResponseData = $statusCode >= 200 && $statusCode < 300 && empty($body);

        return ($checkMethods || $checkResponseData);
    }

    /**
     * @param string $endpoint
     * @param object|null $parameters
     *
     * @return ResponseInterface
     */
    private function get(string $endpoint, ?object $parameters): ResponseInterface
    {
        $response = $this->guzzleClient->request('GET', $endpoint, [
            'query' => is_null($parameters) ? '' : $parameters->toArray(),
        ]);

        return $response;
    }

    /**
     * @param string $endpoint
     * @param object|null $parameters
     *
     * @return ResponseInterface
     */
    private function post(string $endpoint, ?object $parameters): ResponseInterface
    {
        $stream = $parameters->stream;

        $options = [
            'json'   => $parameters->toArray(),
            'stream' => $stream,
        ];

        $response = $this->guzzleClient->request('POST', $endpoint, $options);

        return $response;
    }

    /**
     * @param string $endpoint
     *
     * @return ResponseInterface
     */
    private function head(string $endpoint): ResponseInterface
    {
        $response = $this->guzzleClient->request('HEAD', $endpoint);
        return $response;
    }

    /**
     * @param string $endpoint
     * @param object|null $parameters Optional parameters object (should implement toArray() method).
     *
     * @return ResponseInterface
     */
    private function delete(string $endpoint, ?object $parameters): ResponseInterface
    {
        $response = $this->guzzleClient->request('DELETE', $endpoint, [
            'json' => $parameters?->toArray(),
        ]);

        return $response;
    }

    /**
     * Checks if the Ollama service is running.
     *
     * Sends a simple GET request to the base URL and verifies if the response
     * body contains "Ollama is running".
     *
     * @return bool True if the service is running, false otherwise.
     */
    public function isRunning(): bool
    {
        try {
            $response = $this->guzzleClient->get('/');
            return $response->getBody()->getContents() === 'Ollama is running';
        } catch (RequestException $e) {
            return false;
        }
    }

    /**
     * Converts a Guzzle RequestException into an OllamaException.
     *
     * @param RequestException $e The caught Guzzle exception.
     *
     * @return OllamaException
     */
    private function createOllamaException(RequestException $e): OllamaException
    {
        $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
        $body = $e->getResponse() ? (string)$e->getResponse()->getBody() : '';

        $message = $e->getMessage();
        if ($body) {
            $message .= " | Response body: " . $body;
        }

        return new OllamaException($message, $statusCode, $e);
    }
}
