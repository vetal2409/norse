<?php

namespace e1\api\v1\controllers;

use Carbon\Carbon;
use e1\Application;
use e1\models\user;
use e1\providers\Eloquent\Model;
use e1\traits\controllerApi;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use e1\Providers\Convert\ConverterServiceProvider;

class auth implements ControllerProviderInterface
{
    use controllerApi {
        connect as public connectTrait;
    }

    public $additions = [

        # appends
        'append.registration' => [],
        'append.login' => [],
        'append.onReload' => [],

        'append.worker.onReload' => [],

        # relations
        'relation.registration' => [],
        'relation.login' => [],
        'relation.onReload' => [],
    ];

    public function connect(\Silex\Application $app)
    {
        /**
         * @var ConverterServiceProvider $converter
         * @var ControllerCollection $controllers
         */

        $converter = $app['сonverter'];
        $controllers = $this->connectTrait($app);

        $controllers->post('/login', [$this, 'login'])->convert('model', function ($m, Request $request) {
            $model = $this->app->model('user')->where('phone', '=', $request->request->get('login', ''))->first();
            return $model ?? $this->app->abort(422);
        })->bind("$this->base_bind.login");

        $controllers->post('/registration', [$this, 'registration'])
            ->convert('model', $converter->get('model', ['modelName' => 'user', 'required' => false]))
            ->bind("$this->base_bind.registration");

        $controllers->get('/on-reload', [$this, 'onReload'])->bind("$this->base_bind.onReload");

        return $controllers;
    }

    public function registration(Model $model, Application $app, Request $request)
    {
        $data = $request->request->all();
        $model->setScenario(user::SCENARIO_REGISTRATION);
        # add appends
        $model->append($this->additions['append.registration'] ?? []);
        # add relations
        $model->load($this->additions['relation.registration'] ?? []);

        if ($model->validate($data) && $model->save()) {
            return $model->toArray();
        }
        return $model->toArray(true);
    }

    public function login(user $model, Request $request, Application $app)
    {
        $data = $request->request->all();

        # add appends
        $model->append($this->additions['append.login'] ?? []);
        # add relations
        $model->load($this->additions['relation.login'] ?? []);

        if ($model->setScenario(user::SCENARIO_LOGIN)->validate($data)) {
            $model->setAttribute('logged_at', Carbon::now());

            if ($model->save()) {
                $this->app['security.user'] = $model;
                return $model->toArray();
            }
            return $app->abort(500);
        }
        return $app->abort(422);
    }

    public function onReload(Request $request, Application $app)
    {
        if (isset($app['security.user']) && $app['security.user']) {
            # add appends
            $app['security.user']->append($this->additions['append.onReload'] ?? []);

            # add relations
            $app['security.user']->load($this->additions['relation.onReload'] ?? []);

            return $app['security.user']->toArray();
        }
        return false;
    }
}
