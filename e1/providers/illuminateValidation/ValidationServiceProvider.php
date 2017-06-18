<?php

namespace e1\providers\illuminateValidation;

use Illuminate\Container\Container;
use Illuminate\Validation\Factory;
use Pimple\ServiceProviderInterface;
use Illuminate\Validation\DatabasePresenceVerifier;

/**
 * Class ValidationServiceProvider
 * @package e1\providers\illuminateValidation
 */
class ValidationServiceProvider implements ServiceProviderInterface
{
    public function register(\Pimple\Container $app)
    {
        $app['validation.presence'] = function () use ($app) {
            return new DatabasePresenceVerifier($app['db.capsule']->getDatabaseManager());
        };

        $app['validator'] = function () use ($app) {

            $validator = new Factory($app['translator'], new Container());
            $validator->setPresenceVerifier($app['validation.presence']);

            return $validator;
        };
    }
}

