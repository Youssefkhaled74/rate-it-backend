<?php

namespace App\Support\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    protected int $statusCode = 400;
    protected ?array $meta = null;

    public function __construct(string $message = "", int $statusCode = 400, ?array $meta = null, ?Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->meta = $meta;
    }

    // New canonical accessors
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    // Backwards compatible aliases
    public function getStatus(): int
    {
        return $this->getStatusCode();
    }

    public function getData(): ?array
    {
        return $this->getMeta();
    }
}
