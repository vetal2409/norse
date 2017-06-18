<?php

namespace e1\site\controllers;

use e1\Models\user;
use e1\Application;
use e1\traits\controllerCore;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class auth implements ControllerProviderInterface
{
    use controllerCore {
        connect as public connectTrait;
        controllerCore::__construct as public coreConstruct;
    }

    protected $afterLogin;
    protected $afterLogout;

    public function __construct(string $layoutTemplate, $afterLogin = null, $afterLogout = null, string $security_checker, string $pathTemplate = '')
    {
        $this->afterLogin = $afterLogin;
        $this->afterLogout = $afterLogout;

        $this->coreConstruct($layoutTemplate, $security_checker, $pathTemplate);
    }

    public function connect(\Silex\Application $app)
    {
        $controllers = $this->connectTrait($app);

        # register
        $controllers->post('/register', [$this, 'registerProcess']);
        $controllers->get('/register', [$this, 'registerForm'])->before(function (Request $request, Application $app) {
            $app['security.authorization_checker']->loadUserCredentials($request);
        })->bind('auth.register');

        # login
        $controllers->get('/', [$this, 'loginForm'])->before(function (Request $request, Application $app) {
            $app['security.authorization_checker']->loadUserCredentials($request);
        })->bind('auth.login');

        $controllers->post('/', [$this, 'loginProcess'])->after(function (Request $request, Response $response, Application $app) {
            $app['security.authorization_checker']->storeUserCredentials($request, $response);
        });

        # logout
        $controllers->get('/logout', [$this, 'logout'])->after(function (Request $request, Response $response, Application $app) {
            $app['security.user'] = null;
            $app['security.authorization_checker']->storeUserCredentials($request, $response);
        })->bind('auth.logout');

        return $controllers;
    }

    # login
    public function loginProcess(Application $app, Request $request)
    {
        $form = $request->request->get('form');

        if (!$form) {
            return $this->render('login', ['error' => 'Under attack']);
        }

        $email = isset($form['email']) ? $form['email'] : '';
        $password = isset($form['password']) ? $form['password'] : '';

        if (!$email || !$password) {
            return $this->render('login', ['error' => "Credentials isn't valid"]);
        }

        /** @var user $user */
        $user = $app->model('user')->where('email', '=', trim($email))->first();

        if (!isset($user->_id) || !$app['security.authorization_checker']->validatePassword($password, $user->password_hash)) {
            return $this->render('login', ['error' => "Credentials isn't valid", 'email' => $email]);
        }

        if (isset($form['remember'])) {
            $app['security.user.permanent'] = 1;
        }

        $app['security.user'] = $user;

        return $app->redirect($app->url($this->afterLogin));
    }

    public function loginForm(Application $app, Request $request)
    {
        if (isset($app['security.user'], $app['security.user']->_id)) {
            $url = $request->headers->get('Referer', $app->url($this->afterLogin));
            return $app->redirect($url);
        }
        return $this->render('login');
    }

    # register
    public function registerForm(Application $app, Request $request)
    {
        if (isset($app['security.user'], $app['security.user']->_id)) {
            $url = $request->headers->get('Referer', $app->url($this->afterLogin));
            return $app->redirect($url);
        }
        return $this->render('register');
    }

    public function registerProcess(Application $app, Request $request)
    {
        /** @var user $model */
        $model = $app->model('user');
        $model->setScenario(user::SCENARIO_REGISTRATION);

        $data = $request->request->get('form', []);

        if ($model->validate($data) && $model->save()) {
            return $app->redirect($app->url('auth.login'));
        }
        return $this->render('register', $model->toArray(true));
    }

    # logout
    public function logout(Application $app, Request $request)
    {
        return $app->redirect($app->url($this->afterLogout));
    }
}