<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Tests\Resources;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Lewenbraun\Ollama\Client;
use Lewenbraun\Ollama\DTO\Requests\CompletionRequest;
use Lewenbraun\Ollama\DTO\Responses\CompletionResponse;
use Lewenbraun\Ollama\Adapters\StreamWrapper;
use Lewenbraun\Ollama\Resources\CompletionResource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CompletionResourceTest extends TestCase
{
    private Client|MockObject $clientMock;
    private CompletionResource $completionResource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientMock = $this->createMock(Client::class);
        $this->completionResource = new CompletionResource($this->clientMock);
    }

    public function testCreateReturnsCompletionResponse(): void
    {
        $parameters = [
            'prompt' => 'Why is the sky blue?',
            'model'  => 'llama3.2',
            'stream' => false,
        ];
        $expectedRequest = CompletionRequest::fromArray($parameters);

        $apiResponseJson = <<<JSON
            {
                "model": "llama3.2",
                "created_at": "2023-08-04T19:22:45.499127Z",
                "response": "The sky is blue because it is the color of the sky.",
                "done": true,
                "context": [1, 2, 3],
                "total_duration": 5043500667,
                "load_duration": 5025959,
                "prompt_eval_count": 26,
                "prompt_eval_duration": 325953000,
                "eval_count": 290,
                "eval_duration": 4709213000
            }
            JSON;
        $apiResponseArray = json_decode($apiResponseJson, true);

        $this->clientMock->expects($this->once())
            ->method('handle')
            ->with(
                'post',
                'generate',
                $this->callback(function (CompletionRequest $request) use ($expectedRequest): bool {
                    $this->assertSame($expectedRequest->toArray(), $request->toArray());
                    $this->assertFalse($request->stream);
                    return true;
                })
            )
            ->willReturn($apiResponseArray);

        $response = $this->completionResource->create($parameters);

        $this->assertInstanceOf(CompletionResponse::class, $response);
        $this->assertSame('llama3.2', $response->model);
        $this->assertSame('The sky is blue because it is the color of the sky.', $response->response);
        $this->assertTrue($response->done);
        $this->assertSame([1, 2, 3], $response->context);
    }

    public function testCreateReturnsStreamWrapperForStreamRequest(): void
    {
        $parameters = [
            'prompt' => 'Tell me a story',
            'model'  => 'llama3.2',
            'stream' => true,
        ];
        $expectedRequest = CompletionRequest::fromArray($parameters);

        $streamChunks = [
            '{"model": "llama3.2", "created_at": "2023-08-04T08:52:19.385406455-07:00", "response": "The", "done": false}',
            '{"model": "llama3.2", "created_at": "2023-08-04T08:52:19.385406455-07:00", "response": " quick", "done": false}',
            '{"model": "llama3.2", "created_at": "2023-08-04T08:52:19.385406455-07:00", "response": " brown", "done": false}',
            '{"model": "llama3.2", "created_at": "2023-08-04T19:22:45.499127Z", "response": "", "done": true, "context": [1, 2, 3], "total_duration": 10706818083, "load_duration": 6338219291, "prompt_eval_count": 26, "prompt_eval_duration": 130079000, "eval_count": 259, "eval_duration": 4232710000}'
        ];
        $fullStreamContent = implode("\n", $streamChunks);

        $stream = Utils::streamFor($fullStreamContent);

        $responseMock = $this->createMock(Response::class);
        $responseMock->method('getBody')->willReturn($stream);

        $this->clientMock->expects($this->once())
            ->method('handle')
            ->with(
                'post',
                'generate',
                $this->callback(function (CompletionRequest $request) use ($expectedRequest): bool {
                    $this->assertSame($expectedRequest->toArray(), $request->toArray());
                    $this->assertTrue($request->stream);
                    return true;
                })
            )
            ->willReturn($responseMock);

        $StreamWrapper = $this->completionResource->create($parameters);

        $this->assertInstanceOf(StreamWrapper::class, $StreamWrapper);

        $expectedResponsesArray = json_decode('[' . implode(',', $streamChunks) . ']', true);
        $expectedResponses = array_map(
            fn ($data) => CompletionResponse::fromArray($data),
            $expectedResponsesArray
        );

        $itemCount = 0;
        foreach ($StreamWrapper as $index => $item) {
            $this->assertInstanceOf(CompletionResponse::class, $item);
            $expectedItem = $expectedResponses[$index];
            $this->assertSame($expectedItem->response, $item->response);
            $this->assertSame($expectedItem->done, $item->done);
            $itemCount++;
        }

        $this->assertSame(
            count($expectedResponses),
            $itemCount
        );
    }
}
