<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Lewenbraun\Ollama\Client;
use Lewenbraun\Ollama\Resources\ModelResource;
use Lewenbraun\Ollama\DTO\Responses\ModelListResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelShowResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelCreateResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelPullResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelPushResponse;
use Lewenbraun\Ollama\DTO\Responses\ModelRunningListResponse;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;

final class ModelResourceTest extends TestCase
{
    public function testCreateModelReturnsModelCreateResponse(): void
    {
        $dummyResponse = ['status' => 'success'];
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo('post'),
                $this->equalTo('create'),
                $this->callback(function ($request) {
                    return property_exists($request, 'stream') && $request->stream === false;
                })
            )
            ->willReturn($dummyResponse);

        $modelResource = new ModelResource($clientMock);
        $parameters = [
            'model'  => 'test-model',
            'from'   => 'base-model',
            'system' => 'Test system',
            'stream' => false,
        ];

        $response = $modelResource->create($parameters);

        $this->assertInstanceOf(ModelCreateResponse::class, $response);
        $this->assertEquals('success', $response->status);
    }

    public function testCreateModelStreamResponse(): void
    {
        $jsonLines = <<<JSON
            {"status": "reading model metadata"}
            {"status": "creating system layer"}
            {"status": "success"}
        JSON;
        $jsonLines .= "\n";

        $stream = Utils::streamFor($jsonLines);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($stream);

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo('post'),
                $this->equalTo('create'),
                $this->callback(function ($request) {
                    return property_exists($request, 'stream') && $request->stream === true;
                })
            )
            ->willReturn($responseMock);

        $modelResource = new ModelResource($clientMock);
        $parameters = [
            'model'  => 'test-model',
            'from'   => 'base-model',
            'system' => 'Test system',
            'stream' => true,
        ];

        $streamWrapper = $modelResource->create($parameters);
        $results = [];
        foreach ($streamWrapper as $response) {
            $results[] = $response;
        }

        $this->assertCount(3, $results);
        $this->assertEquals("reading model metadata", $results[0]->status);
        $this->assertEquals("creating system layer", $results[1]->status);
        $this->assertEquals("success", $results[2]->status);
    }

    public function testListModelsReturnsModelListResponse(): void
    {
        $dummyResponse = [
            'models' => [
                [
                    'parent_model'       => '',
                    'format'             => 'gguf',
                    'family'             => 'llama',
                    'families'           => ['llama'],
                    'parameter_size'     => '13B',
                    'quantization_level' => 'Q4_0',
                ],
            ],
        ];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with('get', 'tags', null)
            ->willReturn($dummyResponse);

        $modelResource = new ModelResource($clientMock);

        $response = $modelResource->list();

        $this->assertInstanceOf(ModelListResponse::class, $response);
        $this->assertCount(1, $response->models);

        $modelDetails = $response->models[0]->toArray();
        $this->assertEquals('gguf', $modelDetails['format']);
    }

    public function testShowModelReturnsModelShowResponse(): void
    {
        $dummyResponse = [
            'modelfile'  => 'dummy modelfile',
            'parameters'  => 'dummy parameters',
            'template'    => 'dummy template',
            'details'     => [
                'parent_model'       => '',
                'format'             => 'gguf',
                'family'             => 'llama',
                'families'           => ['llama'],
                'parameter_size'     => '8B',
                'quantization_level' => 'Q4_0',
            ],
            'model_info'  => ['key' => 'value'],
        ];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo('post'),
                $this->equalTo('show'),
                $this->callback(function ($request) {
                    return property_exists($request, 'model') && $request->model === 'llama3.2';
                })
            )
            ->willReturn($dummyResponse);

        $modelResource = new ModelResource($clientMock);
        $parameters = ['model' => 'llama3.2'];

        $response = $modelResource->show($parameters);

        $this->assertInstanceOf(ModelShowResponse::class, $response);
        $this->assertEquals('dummy modelfile', $response->modelfile);
        $this->assertEquals('dummy parameters', $response->parameters);
        $this->assertEquals('dummy template', $response->template);
        $this->assertEquals(['key' => 'value'], $response->model_info);
    }

    public function testCopyModelReturnsTrueOnSuccess(): void
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo('post'),
                $this->equalTo('copy'),
                $this->callback(function ($request) {
                    return property_exists($request, 'source') && property_exists($request, 'destination')
                        && $request->source === 'llama3.2'
                        && $request->destination === 'llama3-backup';
                })
            )
            ->willReturn(200);

        $modelResource = new ModelResource($clientMock);
        $parameters = [
            'source'      => 'llama3.2',
            'destination' => 'llama3-backup',
        ];

        // Действие
        $result = $modelResource->copy($parameters);

        // Проверка
        $this->assertTrue($result);
    }

    public function testDeleteModelReturnsTrueOnSuccess(): void
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo('delete'),
                $this->equalTo('delete'),
                $this->callback(function ($request) {
                    return property_exists($request, 'model') && $request->model === 'llama3:13b';
                })
            )
            ->willReturn(200);

        $modelResource = new ModelResource($clientMock);
        $parameters = ['model' => 'llama3:13b'];

        $result = $modelResource->delete($parameters);

        $this->assertTrue($result);
    }

    public function testPullModelReturnsModelPullResponse(): void
    {
        $dummyResponse = ['status' => 'success'];
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo('post'),
                $this->equalTo('pull'),
                $this->callback(function ($request) {
                    return property_exists($request, 'model') && $request->model === 'llama3.2'
                        && property_exists($request, 'stream') && $request->stream === false;
                })
            )
            ->willReturn($dummyResponse);

        $modelResource = new ModelResource($clientMock);
        $parameters = [
            'model'  => 'llama3.2',
            'stream' => false,
        ];

        $response = $modelResource->pull($parameters);

        $this->assertInstanceOf(ModelPullResponse::class, $response);
        $this->assertEquals('success', $response->status);
    }

    public function testPullModelStreamResponse(): void
    {
        $jsonLines = <<<JSON
            {"status": "pulling manifest"}
            {"status": "downloading digest", "digest": "sha256:dummy", "total": 123, "completed": 50}
            {"status": "success"}
        JSON;
        $jsonLines .= "\n";
        $stream = Utils::streamFor($jsonLines);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($stream);

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo('post'),
                $this->equalTo('pull'),
                $this->callback(function ($request) {
                    return property_exists($request, 'stream') && $request->stream === true;
                })
            )
            ->willReturn($responseMock);

        $modelResource = new ModelResource($clientMock);
        $parameters = [
            'model'  => 'llama3.2',
            'stream' => true,
        ];

        $streamWrapper = $modelResource->pull($parameters);
        $results = [];
        foreach ($streamWrapper as $response) {
            $results[] = $response;
        }

        $this->assertCount(3, $results);
        $this->assertEquals("pulling manifest", $results[0]->status);
        $this->assertEquals("downloading digest", $results[1]->status);
        $this->assertEquals("success", $results[2]->status);
    }


    public function testPushModelReturnsModelPushResponse(): void
    {
        $dummyResponse = ['status' => 'success'];
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo('post'),
                $this->equalTo('push'),
                $this->callback(function ($request) {
                    return property_exists($request, 'model') && $request->model === 'mattw/pygmalion:latest'
                        && property_exists($request, 'stream') && $request->stream === false;
                })
            )
            ->willReturn($dummyResponse);

        $modelResource = new ModelResource($clientMock);
        $parameters = [
            'model'  => 'mattw/pygmalion:latest',
            'stream' => false,
        ];

        $response = $modelResource->push($parameters);

        $this->assertInstanceOf(ModelPushResponse::class, $response);
        $this->assertEquals('success', $response->status);
    }

    public function testPushModelStreamResponse(): void
    {
        $jsonLines = <<<JSON
            {"status": "retrieving manifest"}
            {"status": "starting upload", "digest": "sha256:dummy", "total": 456}
            {"status": "pushing manifest"}
            {"status": "success"}
        JSON;
        $jsonLines .= "\n";
        $stream = Utils::streamFor($jsonLines);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($stream);

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo('post'),
                $this->equalTo('push'),
                $this->callback(function ($request) {
                    return property_exists($request, 'stream') && $request->stream === true;
                })
            )
            ->willReturn($responseMock);

        $modelResource = new ModelResource($clientMock);
        $parameters = [
            'model'  => 'mattw/pygmalion:latest',
            'stream' => true,
        ];

        $streamWrapper = $modelResource->push($parameters);
        $results = [];
        foreach ($streamWrapper as $response) {
            $results[] = $response;
        }

        $this->assertCount(4, $results);
        $this->assertEquals("retrieving manifest", $results[0]->status);
        $this->assertEquals("starting upload", $results[1]->status);
        $this->assertEquals("pushing manifest", $results[2]->status);
        $this->assertEquals("success", $results[3]->status);
    }

    public function testListRunningModelsReturnsModelRunningListResponse(): void
    {
        $dummyResponse = [
            'models' => [
                [
                    'name'      => 'running-model-1',
                    'model'     => 'llama3.2',
                    'size'      => 456,
                    'digest'    => 'sha256:dummy',
                    'details'   => [
                        'parent_model'       => '',
                        'format'             => 'gguf',
                        'family'             => 'llama',
                        'families'           => ['llama'],
                        'parameter_size'     => '8B',
                        'quantization_level' => 'Q4_0',
                    ],
                    'expires_at' => '2023-01-01T00:00:00+00:00',
                    'size_vram' => 456,
                ],
            ],
        ];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('handle')
            ->with('get', 'ps')
            ->willReturn($dummyResponse);

        $modelResource = new ModelResource($clientMock);

        $response = $modelResource->listRunning();

        $this->assertInstanceOf(ModelRunningListResponse::class, $response);
        $this->assertCount(1, $response->models);

        $modelRunningData = $response->models[0]->toArray();
        $this->assertEquals('running-model-1', $modelRunningData['name']);
    }
}
