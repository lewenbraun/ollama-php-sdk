## Ollama PHP Client Library

A lightweight PHP client library to interact with the Ollama API. This library provides a simple, fluent interface to work with text and chat completions, manage models, handle blobs, and generate embeddings.

## Installation

Install the library via Composer:

```bash
composer require lewenbraun/ollama-php-sdk
```

## Usage

### Initialize the Client

Create a new client instance (default host is `http://localhost:11434`):

```php
use Lewenbraun\Ollama\Ollama;

$client = Ollama::client('http://localhost:11434');
```

### Completion

Generate a text completion using a model. Under the hood, this wraps the `/api/generate` endpoint.

#### Non-Streaming Completion

Returns a single response object:

```php
$completion = $client->completion()->create([
    'model'  => 'llama3.2',
    'prompt' => 'Hello',
    // 'stream' is false by default
]);

echo $completion->response;
```

#### Streaming Completion

Enables streaming to receive the response in chunks:

```php
$completion = $client->completion()->create([
    'model'  => 'llama3.2',
    'prompt' => 'Hello',
    'stream' => true,
]);

foreach ($completion as $chunk) {
    echo $chunk->response;
}
```

### Chat Completion

Generate chat responses using a conversational context. This wraps the `/api/chat` endpoint.

#### Non-Streaming Chat Completion

Returns the final chat response as a single object:

```php
$chat = $client->chatCompletion()->create([
    'model'    => 'llama3.2',
    'messages' => [
        ['role' => 'user', 'content' => 'why is the sky blue?']
    ],
    'stream' => false,
]);

echo $chat->message->content;
```

#### Streaming Chat Completion

Stream the chat response as it is generated:

```php
$chat = $client->chatCompletion()->create([
    'model'    => 'llama3.2',
    'messages' => [
        ['role' => 'user', 'content' => 'why is the sky blue?']
    ],
    'stream' => true,
]);

foreach ($chat as $chunk) {
    echo $chunk->message->content;
}
```

### Model Management

Manage models through various endpoints (create, list, show, copy, delete, pull, push).

#### Create a Model

Create a new model from an existing model (or other sources):

```php
$newModel = $client->models()->create([
    'model'  => 'mario',
    'from'   => 'llama3.2',
    'system' => 'You are Mario from Super Mario Bros.',
]);
```

#### List Models

Retrieve a list of available models:

```php
$modelList = $client->models()->list();
print_r($modelList);
```

#### List Running Models

List models currently loaded into memory:

```php
$runningModels = $client->models()->listRunning();
print_r($runningModels);
```

#### Show Model Information

Retrieve details about a specific model:

```php
$modelInfo = $client->models()->show(['model' => 'llama3.2']);
print_r($modelInfo);
```

#### Copy a Model

Create a copy of an existing model:

```php
$copied = $client->models()->copy([
    'source'      => 'llama3.2',
    'destination' => 'llama3-backup',
]);
```

#### Delete a Model

Delete a model by name:

```php
$deleted = $client->models()->delete(['model' => 'llama3:13b']);
```

#### Pull a Model

Download a model from the Ollama library:

```php
$pull = $client->models()->pull(['model' => 'llama3.2']);
```

#### Push a Model

Upload a model to your model library:

```php
$push = $client->models()->push(['model' => 'your_namespace/your_model:tag']);
```

### Blobs

Work with binary large objects (blobs) used for model files.

#### Check if a Blob Exists

Verify if a blob (by its SHA256 digest) exists on the server:

```php
$exists = $client->blobs()->exists(['digest' => 'sha256:your_digest_here']);
```

#### Push a Blob

Upload a blob to the Ollama server:

```php
$blobPushed = $client->blobs()->push([
    'digest' => 'sha256:your_digest_here',
    // Additional parameters as required
]);
```



### Embeddings

Generate embeddings from a model using the `/api/embed` endpoint.

```php
$embedding = $client->embed()->create([
    'model' => 'all-minilm',
    'input' => 'Why is the sky blue?',
]);

print_r($embedding);
```

### Version

Retrieve the version of the Ollama server.

```php
$version = $client->version()->show();
echo "Ollama version: " . $version->version;
```

### Advanced Parameters

Each method accepts additional parameters (e.g., suffix, format, options, keep_alive, etc.) that can be used to fine-tune the request. For example, you can pass advanced options in the request arrays for structured outputs, reproducible responses, and more. For full details, refer to the **[Ollama API documentation](https://github.com/ollama/ollama/blob/main/docs/api.md)**.


## Contributing

Contributions, issues, and feature requests are welcome. Please check the issues page for more details.

## License

This project is licensed under the MIT License.
