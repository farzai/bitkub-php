<?php

namespace Farzai\Bitkub\Exceptions;

class BadRequestException extends \Exception
{
    public function __construct($message = 'Bad Request', $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
