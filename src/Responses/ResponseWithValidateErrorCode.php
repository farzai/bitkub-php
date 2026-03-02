<?php

namespace Farzai\Bitkub\Responses;

use Farzai\Bitkub\Exceptions\BitkubResponseErrorCodeException;
use Farzai\Transport\Contracts\ResponseInterface;

class ResponseWithValidateErrorCode extends AbstractResponseDecorator
{
    /**
     * Throw an exception if the response is not successful.
     *
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function throw(?callable $callback = null): static
    {
        parent::throw($callback ?? function (ResponseInterface $response, ?\Exception $e) use ($callback) {
            $errorCode = $this->json('error');
            if ($errorCode !== null && $errorCode !== 0) {
                throw new BitkubResponseErrorCodeException($response);
            }

            return $callback ? $callback($response, $e) : $response;
        });

        return $this;
    }
}
