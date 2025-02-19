<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Tests\Resources;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Lewenbraun\Ollama\Client;
use Lewenbraun\Ollama\DTO\Requests\ChatCompletionRequest;
use Lewenbraun\Ollama\DTO\Responses\ChatCompletionResponse;
use Lewenbraun\Ollama\Adapters\StreamWrapper;
use Lewenbraun\Ollama\Resources\ChatCompletionResource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ChatCompletionResourceTest extends TestCase
{
    private Client|MockObject $clientMock;
    private ChatCompletionResource $chatCompletionResource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientMock = $this->createMock(Client::class);
        $this->chatCompletionResource = new ChatCompletionResource($this->clientMock);
    }

    public function testCreateReturnsChatCompletionResponse(): void
    {
        $parameters = [
            'model'    => 'llama3.2',
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => 'Hello, how are you?'
                ]
            ],
            'stream'   => false,
        ];
        $expectedRequest = ChatCompletionRequest::fromArray($parameters);

        $apiResponseJson = <<<JSON
            {
                "model": "llama3.2",
                "created_at": "2023-12-12T14:13:43.416799Z",
                "message": {
                    "role": "assistant",
                    "content": "Hello! How are you today?",
                    "images": null,
                    "tool_calls": null
                },
                "done": true,
                "total_duration": 5191566416,
                "load_duration": 2154458,
                "prompt_eval_count": 26,
                "prompt_eval_duration": 383809000,
                "eval_count": 298,
                "eval_duration": 4799921000
            }
            JSON;
        $apiResponseArray = json_decode($apiResponseJson, true);

        $this->clientMock->expects($this->once())
            ->method('handle')
            ->with(
                'post',
                'chat',
                $this->callback(function (ChatCompletionRequest $request) use ($expectedRequest): bool {
                    $this->assertEquals($expectedRequest->toArray(), $request->toArray());
                    $this->assertFalse($request->stream);
                    return true;
                })
            )
            ->willReturn($apiResponseArray);

        $response = $this->chatCompletionResource->create($parameters);

        $this->assertInstanceOf(ChatCompletionResponse::class, $response);
        $this->assertSame('llama3.2', $response->model);
        $this->assertSame("Hello! How are you today?", $response->message->toArray()['content']);
        $this->assertTrue($response->done);
        $this->assertSame(5191566416, $response->totalDuration);
    }

    public function testCreateReturnsStreamWrapperForStreamRequest(): void
    {
        $parameters = [
            'model'    => 'llama3.2',
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => 'Tell me a story'
                ]
            ],
            'stream'   => true,
        ];
        $expectedRequest = ChatCompletionRequest::fromArray($parameters);

        $streamChunks = [
            '{"model": "llama3.2", "created_at": "2023-08-04T08:52:19.385406455-07:00", "message": {"role": "assistant", "content": "Once", "images": null, "tool_calls": null}, "done": false}',
            '{"model": "llama3.2", "created_at": "2023-08-04T08:52:20.123456789-07:00", "message": {"role": "assistant", "content": "upon a time", "images": null, "tool_calls": null}, "done": false}',
            '{"model": "llama3.2", "created_at": "2023-08-04T08:52:21.987654321-07:00", "message": {"role": "assistant", "content": "in a faraway land", "images": ["base64image1", "base64image2"], "tool_calls": [{"function": {"name": "get_current_weather", "arguments": {"location": "Paris, FR", "format": "celsius"}}}]}, "done": true, "total_duration": 150000, "load_duration": 75000, "prompt_eval_count": 15, "prompt_eval_duration": 90000000, "eval_count": 100, "eval_duration": 300000000}'
        ];
        $fullStreamContent = implode("\n", $streamChunks);
        $stream = Utils::streamFor($fullStreamContent);

        $responseMock = $this->createMock(Response::class);
        $responseMock->method('getBody')->willReturn($stream);

        $this->clientMock->expects($this->once())
            ->method('handle')
            ->with(
                'post',
                'chat',
                $this->callback(function (ChatCompletionRequest $request) use ($expectedRequest): bool {
                    $this->assertEquals($expectedRequest->toArray(), $request->toArray());
                    $this->assertTrue($request->stream);
                    return true;
                })
            )
            ->willReturn($responseMock);

        $StreamWrapper = $this->chatCompletionResource->create($parameters);
        $this->assertInstanceOf(StreamWrapper::class, $StreamWrapper);

        $expectedResponsesArray = json_decode('[' . implode(',', $streamChunks) . ']', true);
        $expectedResponses = array_map(
            fn ($data) => ChatCompletionResponse::fromArray($data),
            $expectedResponsesArray
        );

        $itemCount = 0;
        foreach ($StreamWrapper as $index => $item) {
            $this->assertInstanceOf(ChatCompletionResponse::class, $item);
            $expectedItem = $expectedResponses[$index];
            $this->assertSame($expectedItem->message->toArray()['content'], $item->message->toArray()['content']);
            $this->assertSame($expectedItem->done, $item->done);
            $itemCount++;
        }
        $this->assertSame(count($expectedResponses), $itemCount);
    }
}
