<?php

namespace Farzai\Bitkub;

use Farzai\Bitkub\Exceptions\InvalidArgumentException;
use Farzai\Transport\TransportBuilder;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\NullLogger;

final class ClientBuilder
{
    const DEFAULT_HOST = 'api.bitkub.com';

    /**
     * The config.
     *
     * @var array<string, mixed>
     */
    private array $config = [];

    /**
     * The HTTP client implementation.
     */
    private ?PsrClientInterface $httpClient = null;

    /**
     * The PSR-3 logger.
     */
    private ?PsrLoggerInterface $logger = null;

    /**
     * The number of times to retry failed requests.
     */
    private $retries = 3;

    /**
     * Create a new client builder instance.
     *
     * @return static
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Set the API credentials.
     *
     * @return $this
     */
    public function setCredentials(string $apiKey, string $secretKey)
    {
        $this->config = array_merge($this->config, [
            'api_key' => $apiKey,
            'secret' => $secretKey,
        ]);

        return $this;
    }

    /**
     * Set the HTTP client implementation.
     *
     * @return $this
     */
    public function setHttpClient(PsrClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * Set the logger implementation.
     *
     * @return $this
     */
    public function setLogger(PsrLoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function setRetries(int $retries)
    {
        if ($retries < 0) {
            throw new \InvalidArgumentException('Retries must be greater than or equal to 0.');
        }

        $this->retries = $retries;

        return $this;
    }

    public function build()
    {
        $this->ensureConfigIsValid();

        $logger = $this->logger ?? new NullLogger();

        $builder = TransportBuilder::make();
        if ($this->httpClient) {
            $builder->setClient($this->httpClient);
        }

        $builder->setLogger($logger);

        $transport = $builder->build();
        $transport->setUri(sprintf('https://%s', self::DEFAULT_HOST));

        $client = new Client(
            config: $this->config,
            transport: $transport,
            logger: $logger,
        );

        return $client;
    }

    private function ensureConfigIsValid(): void
    {
        if (empty($this->config['api_key'])) {
            throw new InvalidArgumentException('API key is required');
        }

        if (empty($this->config['secret'])) {
            throw new InvalidArgumentException('Secret key is required');
        }
    }

    final public function __construct()
    {

    }
}
