<?php

namespace e1\providers\Convert;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ConverterServiceProvider
 * @package e1\providers\Convert
 *
 * @property \e1\Application $app
 * @property array $converters
 */
class ConverterServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    protected $app;
    protected $converters = [];

    public function boot(Application $app)
    {
        $app['converter.init']($app);
    }

    public function register(Container $app)
    {
        $this->app = $app;
        $app['сonverter'] = $this;

        $app['converter.init'] = $app->protect(function ($app) {

            $converters = $app['converter.callbacks'] ?? [];

            foreach ($converters as $class) {

                if (!class_exists($class)) {
                    throw new \Exception("$class does not exist", 500);
                }

                if (!(new \ReflectionClass($class))->isSubclassOf(ConverterCore::class)) {
                    throw new \Exception("instanceof class an ConverterCore instance, $class given", 500);
                }

                $this->add($class::converterName(), function (array $params) use ($class) {
                    return new $class($this->app, $params);
                });
            }
        });
    }

    public function add(string $name, callable $callback)
    {
        $this->converters[$name] = $callback;
    }

    public function get(string $name, array $params = [], string $method = 'convert')
    {
        return function ($attribute = null, Request $request) use ($name, $params, $method) {
            return call_user_func([$this->converters[$name]($params), $method], $attribute, $request);
        };
    }
}