<?php

declare(strict_types=1);

namespace Farzai\Bitkub\Exceptions;

use Farzai\Bitkub\Constants\ErrorCodes;
use Farzai\Support\Arr;
use Farzai\Transport\Contracts\ResponseInterface as FarzaiResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class BitkubResponseErrorCodeException extends \Exception
{
    /**
     * The response instance.
     */
    protected PsrResponseInterface $response;

    /**
     * Create a new exception instance.
     */
    public function __construct(PsrResponseInterface $response)
    {
        $this->response = $response;

        if ($response instanceof FarzaiResponseInterface) {
            $errorCode = $response->json('error');
        } else {
            $jsonBody = json_decode($response->getBody()->getContents(), true) ?? [];
            $errorCode = Arr::get($jsonBody, 'error');
        }

        if (is_null($errorCode)) {
            parent::__construct('Malformed response: error code not found.', 0);

            return;
        }

        $message = ErrorCodes::getDescription($errorCode);

        parent::__construct($message, $errorCode);
    }

    /**
     * Get the response instance.
     */
    public function getResponse(): PsrResponseInterface
    {
        return $this->response;
    }
}
