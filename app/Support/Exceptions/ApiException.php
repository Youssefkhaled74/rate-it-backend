<?php

namespace App\Support\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected int $status;

    public function __construct(string $message = "", int $status = 422)
    {
        parent::__construct($message);
        $this->status = $status;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
