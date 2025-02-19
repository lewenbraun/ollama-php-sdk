<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO;

abstract class Request
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
