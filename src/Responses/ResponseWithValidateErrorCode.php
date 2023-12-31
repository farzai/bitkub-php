<?php

namespace Farzai\Bitkub\Responses;

use Farzai\Bitkub\Exceptions\BitkubResponseErrorCodeException;
use Farzai\Transport\Contracts\ResponseInterface;

class ResponseWithValidateErrorCode extends AbstractResponseDecorator
{
    /**
     * Throw an exception if the response is not successfull.
     *
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function throw(?callable $callback = null)
    {
        return parent::throw($callback ?? function (ResponseInterface $response, ?\Exception $e) use ($callback) {
            if ($this->json('error') !== null && $this->json('error') !== 0) {
                throw new BitkubResponseErrorCodeException($response);
            }

            return $callback ? $callback($response, $e) : $response;
        });
    }
}
