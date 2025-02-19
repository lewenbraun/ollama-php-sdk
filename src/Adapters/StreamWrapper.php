<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\Adapters;

use Generator;
use JsonException;
use IteratorAggregate;
use Psr\Http\Message\ResponseInterface;
use Lewenbraun\Ollama\Responses\Exceptions\StreamWrapperException;

final class StreamWrapper implements IteratorAggregate
{
    private const READ_BUFFER_SIZE = 4096;

    public function __construct(
        private readonly string $responseClass,
        private readonly ResponseInterface $response,
    ) {
    }

    /**
     * An iterator for processing a stream line by line.
     *
     * @return Generator
     * @throws StreamWrapperException
     */
    public function getIterator(): Generator
    {
        $stream = $this->response->getBody();
        $buffer = '';

        while (!$stream->eof()) {
            $chunk = $stream->read(self::READ_BUFFER_SIZE);
            if ($chunk === '') {
                break;
            }
            $buffer .= $chunk;

            // Find the line separator
            while (($newlinePos = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $newlinePos);
                $buffer = substr($buffer, $newlinePos + 1);

                if ($data = $this->decodeJsonLine($line)) {
                    yield $this->responseClass::fromArray($data);
                }
            }
        }

        // If there is data left in the buffer, process it
        if ($buffer !== '' && ($data = $this->decodeJsonLine($buffer)) !== null) {
            yield $this->responseClass::fromArray($data);
        }
    }

    /**
    * Decodes a JSON string and validates it.
     *
     * @param string $line
     * @return array|null Returns an array of data or null if the string is empty
     * @throws StreamWrapperException
     */
    private function decodeJsonLine(string $line): ?array
    {
        $line = trim($line);
        if ($line === '') {
            return null;
        }

        try {
            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new StreamWrapperException(
                "JSON decode failed: {$e->getMessage()}. Raw data: {$line}",
                0,
                $e
            );
        }

        if (!empty($data['error'])) {
            throw new StreamWrapperException(
                "API error: {$data['error']}",
                $data['code'] ?? 0
            );
        }

        return $data;
    }
}
