<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Requests;

use Lewenbraun\Ollama\DTO\Request;

class BlobPushRequest extends Request
{
    /**
     * @param string $digest (required) the expected SHA256 digest of the file
     * @param string $filePath (required) path to the file to be pushed as a blob
     */
    public function __construct(
        public readonly string $digest,
        public readonly string $filePath
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            digest: $data['digest'],
            filePath: $data['file_path'],
        );
    }

    public function toArray(): array
    {
        return [
            'digest' => $this->digest,
            'file_path' => $this->filePath,
        ];
    }
}
