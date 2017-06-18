<?php

namespace e1\providers\RBAC;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class tokenServiceProvider
 * @package e1\providers\RBAC
 */
class tokenServiceProvider extends security implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $this->app = $app;
        $app[$this->name_container] = $this;
    }

    public function isGranted(array $roles = ['*'], Request $request): bool
    {
        if (!isset($this->app['security.user'])) {
            $this->loadUserCredentials($request);
        }

        if (!isset($this->app['security.user'])) {
            return false;
        }

        return $roles !== ['*'] ? $this->app['security.user']->isRole($roles) : true;
    }

    public function storeUserCredentials(Request $request, Response $response): bool
    {
        return true;
    }

    public function loadUserCredentials(Request $request): bool
    {
        $x_token = $request->headers->get('x-access-token', $request->get('x_token', ''));

        if ($user = $this->app->model('user')->where('token', '=', $x_token)->first()) {

            $user->increment('requests');
            $this->app['security.user'] = $user;

            return true;
        }
        return false;
    }
}

