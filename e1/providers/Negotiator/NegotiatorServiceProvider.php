<?php

namespace e1\providers\Negotiator;

use Pimple\Container;
use Negotiation\Negotiator;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

/**
 * Class NegotiatorServiceProvider
 * @package e1\providers\Negotiator
 *
 * @property \e1\Application $app
 * @property Negotiator $client
 * @property array $headers
 *
 * @REQUIRE
 *
 * @see https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md
 * composer require willdurand/negotiation
 *
 * @REGISTER:
 * $app->register(new \e1\providers\Negotiator\NegotiatorServiceProvider(), [
 *      'negotiator.headers' => [
 *          'Accept' => ['text/html', 'application/json'],
 *      ]
 * ]);
 *
 * @USAGE:
 *
 * $app['negotiator']->isFormat('Accept', 'application/json');
 *
 * $app['negotiator']->isFormat('Accept', 'text/html');
 *
 */
class NegotiatorServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    protected $app;
    protected $client;
    protected $headers;

    public function boot(Application $app)
    {
        $this->headers = $app['negotiator.headers'] ?? [];
    }

    public function register(Container $app)
    {
        $this->app = $app;
        $app['negotiator'] = $this;
        $this->client = new Negotiator();

        $app['negotiator.init'] = function () use ($app) {
            $this->headers = $app['negotiator.headers'] ?? [];
        };
    }

    /**
     * Get Best Match in header http.
     *
     * @param string $header
     * @return \Negotiation\AcceptHeader|null
     */
    public function bestMatch(string $header)
    {
        $string = $this->app['request_stack']->getCurrentRequest()->headers->get($header);

        return $this->client->getBest($string, $this->headers[$header] ?? []);
    }

    /**
     * Check is type header in array.
     *
     * @param string $header
     * @param array ...$types
     * @return bool
     */
    public function isFormat(string $header, ...$types): bool
    {
        if ($acceptHeader = $this->bestMatch($header)) {
            return in_array($acceptHeader->getType(), $types, false);
        }
        return false;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters = [])
    {
        return $this->client->{$method}(...$parameters);
    }
}