<?php

namespace Lewenbraun\Ollama\Responses\Exceptions;

use Exception;
use Throwable;

class StreamWrapperException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Stream Response Error: " . $message, $code, $previous);
    }
}
