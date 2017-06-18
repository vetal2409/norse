<?php

namespace e1\providers\Cache;

use Predis\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use e1\providers\Cache\Interfaces\FormatterInterface;

/**
 * Class RedisServiceProvider
 * @package e1\providers\Telegram\Rbac
 *
 * @property \e1\Application $app
 * @property FormatterInterface $formatter
 * @property Client $redis
 * @property string $prefix
 *
 * @REQUIRE:
 * @see https://github.com/nrk/predis
 * - composer require predis/predis
 *
 * @REGISTER:
 * $app->register(new \e1\providers\Cache\PredisCacheServiceProvider([
 *   'host' => '127.0.0.1',
 *   'port' => '6379',
 *   'database' => '0',
 *   ], ['prefix' => 'cache:']), [
 *     'cache.callback' => [
 *     'schedule' => [\e1\Models\schedule::class, 'refreshCache'],
 *   ]
 * ]);
 *
 * @USAGE:
 * # set
 * $app['cache.redis']->set('key', $model->get()); # Arrayable model
 * # get
 * $result = $app['cache.redis']->get('key');
 * # twig extension
 * cache('cache.redis','auth_rules', [], true)
 */
class PredisCacheServiceProvider extends Cache implements ServiceProviderInterface, BootableProviderInterface
{
    protected $redis;
    protected $callback;

    public function register(Container $app)
    {
        $this->app = $app;
        $app['cache.redis'] = $this;
    }

    public function boot(\Silex\Application $app)
    {
        $this->callback = $app['cache.callback'];
    }

    public function __construct($parameters = null, $options = null, FormatterInterface $formatter = null)
    {
        $this->redis = new Client($parameters, $options);

        $this->prefix = $this->redis->getOptions()->prefix;
    }

    public function client()
    {
        return $this->redis;
    }

    public function set(string $key, $value, $seconds = null)
    {
        $this->redis->set($key, $this->getFormatter()->setFormat($value));

        if ($seconds) {
            $this->redis->expire($key, $seconds);
        }

        return $this->has($key);
    }

    public function get(string $key, $default = null, $formatter = true)
    {
        if ($this->has($key)) {
            $data = $this->redis->get($key);
            return $formatter ? $this->getFormatter()->getFormat($data) : $data;
        } elseif ($this->refresh($key)) {
            return $this->get($key, $default, $formatter);
        }
        return $default;
    }

    public function has(string $key): bool
    {
        return $this->redis->exists($key);
    }

    public function remove(string $key): bool
    {
        return $this->redis->del([$key]);
    }

    public function increment($key, $value = 1)
    {
        return $this->redis->incrby($key, $value);
    }

    public function decrement($key, $value = 1)
    {
        return $this->redis->decrby($key, $value);
    }

    public function flush()
    {
        return $this->redis->flushdb();
    }

    public function refresh(string $key): bool
    {
        $keyCallback = $key;

        do {
            $position = strrpos($keyCallback, ':');
            $keyCallback = substr($keyCallback, 0, -(strlen($keyCallback) - $position));

            # проверяем если есть ключ в массиве $this->callback
            if (array_key_exists($keyCallback, $this->callback)) {
                return call_user_func_array($this->callback[$keyCallback], [$key, $this->app]);
            }
        } while ($position);

        return false;
    }
}

