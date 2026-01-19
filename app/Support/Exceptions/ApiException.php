<?php

namespace App\Support\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected int $status;
    protected ?array $data = null;

    public function __construct(string $message = "", int $status = 422, ?array $data = null)
    {
        parent::__construct($message);
        $this->status = $status;
        $this->data = $data;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
