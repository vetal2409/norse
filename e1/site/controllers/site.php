<?php

namespace e1\site\controllers;

use Silex;
use e1\Application;
use e1\traits\controllerCore;
use Symfony\Component\HttpFoundation\Request;

class site implements Silex\Api\ControllerProviderInterface
{
    use controllerCore {
        connect as public connectTrait;
    }

    public function connect(Silex\Application $app)
    {
        $converter = $app['сonverter'];
        $controllers = $this->connectTrait($app);

        $controllers->get('/dashboard', [$this, 'dashboard'])->bind("$this->modelName.dashboard");

        return $controllers;
    }

    public function dashboard(Application $app, Request $request)
    {
        return $this->render('dashboard');
    }
}