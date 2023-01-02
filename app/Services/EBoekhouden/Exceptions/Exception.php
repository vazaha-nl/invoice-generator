<?php

namespace App\Services\EBoekhouden\Exceptions;

use Exception as GlobalException;
use Throwable;

class Exception extends GlobalException
{
    protected string $errorCode;

    protected string $errorMessage;

    protected object $result;

    public function __construct(string $message, string $code, Throwable $previous = null) {
        $this->errorCode = $code;
        $this->errorMessage = $message;

        parent::__construct($code . ' ' . $message, 0, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
