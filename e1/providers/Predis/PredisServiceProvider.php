<?php

namespace e1\providers\Predis;

use \Predis\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

/**
 * Class PredisServiceProvider
 * @package e1\providers\Predis
 *
 * @property \e1\Application $app
 * @property Client $client
 * @property string $prefix
 *
 *
 * @REQUIRE
 *
 * @see https://github.com/nrk/predis
 * composer require predis/predis
 *
 * @REGISTER:
 *
 * $app->register(new e1\providers\Predis\PredisServiceProvider([
 *      'host' => '127.0.0.1',
 *      'port' => '6379',
 *      'database' => '1',
 *      'read_write_timeout' => 0,
 * ]));
 *
 * @USAGE:
 *
 * $app['redis']->publish('channel_1',$data);
 *
 * $app['redis']->subscribe(['channel_1', 'channel_2'], function ($payload, $channel) use ($app) {
 *          # ...
 * });
 *
 *  ...
 */
class PredisServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    protected $app;
    protected $client;
    protected $prefix;

    public function boot(Application $app)
    {
        $app['redis.init'];
    }

    public function register(Container $app)
    {
        $this->app = $app;
        $app['redis'] = $this;

        $app['redis.init'] = function () use ($app) {

            $parameters = $app['redis.parameters'] ?? null;
            $options = $app['redis.options'] ?? null;

            $this->client = new Client($parameters, $options);

            # get prefix database
            $this->prefix = $this->client->getOptions()->prefix;
        };
    }

    /**
     * Get Redis client.
     *
     * @return Client
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Subscribe to a set of given channels for messages.
     *
     * @param  array|string $channels
     * @param  callable $callback
     * @return void
     */
    public function subscribe($channels, callable $callback)
    {
        /** @var array $loop */
        $loop = $this->client->pubSubLoop();

        call_user_func_array([$loop, __FUNCTION__], (array)$channels);

        foreach ($loop as $message) {
            if ($message->kind === 'message') {
                $callback($message->payload, $message->channel);
            }
        }

        unset($loop);
    }

    /**
     * Subscribe to a set of given channels with wildcards.
     *
     * @param  string $channel
     * @param  mixed $message
     * @return void
     */
    public function publish(string $channel, $message)
    {
        $this->client->publish($channel, serialize($message));
    }

    /**
     * Run a command against the Redis.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters = [])
    {
        return $this->client->{$method}(...$parameters);
    }
}