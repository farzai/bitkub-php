<?php

namespace Farzai\Bitkub;

use Farzai\Bitkub\Responses\ResponseWithValidateErrorCode;
use Farzai\Transport\Contracts\ResponseInterface;
use Farzai\Transport\Request;
use Farzai\Transport\Response;
use Farzai\Transport\Transport;
use Farzai\Transport\TransportBuilder;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Log\LoggerInterface;

class Client
{
    const BASE_URI = 'https://api.bitkub.com';

    protected $secureEndpoints = [
        // V3
        'POST /api/v3/market/wallet',
        'POST /api/v3/user/trading-credits',
        'POST /api/v3/market/place-bid',
        'POST /api/v3/market/place-ask',
        'POST /api/v3/market/cancel-order',
        'POST /api/v3/market/balances',
        'GET /api/v3/market/my-open-orders',
        'GET /api/v3/market/my-order-history',
        'GET /api/v3/market/order-info',
        'POST /api/v3/crypto/addresses',
        'POST /api/v3/crypto/withdraw',
        'POST /api/v3/crypto/internal-withdraw',
        'POST /api/v3/crypto/deposit-history',
        'POST /api/v3/crypto/withdraw-history',
        'POST /api/v3/crypto/generate-address',
        'POST /api/v3/fiat/accounts',
        'POST /api/v3/fiat/withdraw',
        'POST /api/v3/fiat/deposit-history',
        'POST /api/v3/fiat/withdraw-history',
        'POST /api/v3/market/wstoken',
        'POST /api/v3/user/limits',
    ];

    /**
     * The transport instance.
     */
    protected Transport $transport;

    /**
     * The config.
     */
    protected array $config;

    /**
     * Create a new client instance.
     */
    public function __construct(
        array $config = [],
        PsrClientInterface $client = null,
        LoggerInterface $logger = null
    ) {
        $this->config = array_merge([
            'base_uri' => self::BASE_URI,
            'api_key' => '',
            'secret' => '',
        ], $config);

        $this->ensureConfigIsValid();

        $builder = TransportBuilder::make();
        if ($client) {
            $builder->setClient($client);
        }

        if ($logger) {
            $builder->setLogger($logger);
        }

        $this->transport = $builder->build();
    }

    public function getStatus(): ResponseInterface
    {
        return $this->get('/api/status');
    }

    /**
     * Get server timestamp.
     */
    public function getServerTimestamp(): ResponseInterface
    {
        $request = $this->createRequest('GET', '/api/v3/servertime');

        // Remove headers
        $request = $request->withoutHeader('Accept')->withoutHeader('Content-Type');

        return new Response($request, $this->transport->sendRequest($request));
    }

    public function getWallet(): ResponseInterface
    {
        return $this->post('/api/v3/market/wallet');
    }

    public function getBalances(): ResponseInterface
    {
        return $this->post('/api/v3/market/balances');
    }

    public function getOpenOrders(string $sym): ResponseInterface
    {
        return $this->get('/api/v3/market/my-open-orders', [
            'query' => [
                'sym' => $sym,
            ],
        ]);
    }

    public function getUserLimits(): ResponseInterface
    {
        return $this->post('/api/v3/user/limits');
    }

    protected function get(string $path, array $options = []): ResponseInterface
    {
        $request = $this->createRequest('GET', $path, $options);

        return $this->createResponse($request, $this->transport->sendRequest($request));
    }

    protected function post(string $path, array $options = []): ResponseInterface
    {
        $request = $this->createRequest('POST', $path, $options);

        return $this->createResponse($request, $this->transport->sendRequest($request));
    }

    protected function ensureRequestAreSecure(PsrRequestInterface $request): PsrRequestInterface
    {
        if ($this->isSecureEndpoint($request->getMethod(), $request->getUri()->getPath())) {
            $authorizer = new Authorizer();

            $uri = $request->getUri();
            $query = $uri->getQuery();
            $body = $request->getBody()->getContents() ?: '';
            $timestamp = (int) (microtime(true) * 1000);

            $signature = $authorizer->generateSignature(
                $this->config['secret'],
                $timestamp,
                $request->getMethod(),
                $uri->getPath(),
                $query,
                $body
            );

            $request = $request
                ->withHeader('X-BTK-APIKEY', $this->config['api_key'])
                ->withHeader('X-BTK-SIGN', $signature)
                ->withHeader('X-BTK-TIMESTAMP', $timestamp);
        }

        return $request;
    }

    /**
     * Determine if the given path is secure.
     */
    protected function isSecureEndpoint(string $method, string $path): bool
    {
        $path = strtoupper($method).' '.$path;

        return in_array($path, $this->secureEndpoints);
    }

    /**
     * Create a new request instance.
     */
    protected function createRequest(string $method, string $path, array $options = []): PsrRequestInterface
    {
        // Normalize path
        $path = '/'.trim($path, '/');

        // Query
        if (isset($options['query'])) {
            $path .= '?'.http_build_query($options['query']);
        }

        // Set body
        if (isset($options['body'])) {
            $body = $options['body'];
            if (is_array($body)) {
                $body = json_encode($body);
            }
        }

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $request = new Request($method, $path, $headers, $body ?? null);

        // Set base URI
        $request = $request->withUri(new Uri(self::BASE_URI));

        // Set path
        $request = $request->withUri($request->getUri()->withPath($path));

        $request = $this->ensureRequestAreSecure($request);

        return $request;
    }

    /**
     * Create a new response instance.
     */
    protected function createResponse(PsrRequestInterface $request, PsrResponseInterface $baseResponse): ResponseInterface
    {
        $response = new Response($request, $baseResponse);
        $response = new ResponseWithValidateErrorCode($response);

        return $response;
    }

    private function ensureConfigIsValid(): void
    {
        if (empty($this->config['api_key'])) {
            throw new \InvalidArgumentException('API key is required');
        }

        if (empty($this->config['secret'])) {
            throw new \InvalidArgumentException('Secret is required');
        }
    }
}
