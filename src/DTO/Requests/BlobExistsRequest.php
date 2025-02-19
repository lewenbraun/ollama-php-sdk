<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Requests;

use Lewenbraun\Ollama\DTO\Request;

class BlobExistsRequest extends Request
{
    /**
     * @param string $digest (required) the SHA256 digest of the blob
     */
    public function __construct(
        public readonly string $digest
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            digest: $data['digest'],
        );
    }
}
