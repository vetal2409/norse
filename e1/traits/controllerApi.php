<?php

namespace e1\traits;

use Silex;
use e1\Application;
use e1\providers\RBAC\security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class coreController
 * @package e1\Controllers
 *
 * @property \e1\Application $app
 * @property string $modelName
 * @property string $prefix
 * @property array $secure
 * @property security $security
 * @property string $security_checker
 * @property string $base_bind
 */
trait controllerApi
{
    protected $app;
    protected $prefix;
    protected $secure;
    protected $modelName;

    protected $base_bind;

    protected $security_checker;


    /**
     * controllerApi constructor.
     * @param string $security_checker
     * @param string $prefix
     * @param null|string $modelName
     * @internal param string $security
     */
    public function __construct(string $security_checker = 'security.authorization_checker', $prefix = 'v1', $modelName = null)
    {
        $this->security_checker = $security_checker;
        $this->modelName = $modelName ?? substr(strrchr(get_called_class(), "\\"), 1);

        $this->prefix = $prefix;
        $this->base_bind = "$this->modelName.$this->prefix";
    }

    /**
     * @param Silex\Application $app
     * @return mixed
     */
    public function connect(Silex\Application $app)
    {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->before(function (Request $request) use ($app, $controllers) {

            $controllers->assert('_locale', $app['translator.locales']);
            $app['security.authorization_checker'] = $app[$this->security_checker];

            if ($this->secure) {

                $bind = $request->get('_route');
                $bool = array_key_exists($bind, array_get($this->secure, 'binds', []));

                $controllers->secure(
                    $bool ? $this->secure['binds'][$bind]['roles'] : $this->secure['roles'],
                    $bool ? $this->secure['binds'][$bind]['redirect_url'] : $this->secure['redirect_url'],
                    $this->security_checker
                );
            }
        });

        return $controllers;
    }

    # todo: secure and access mv to another trait

    /**
     * @param array $roles
     * @param null|string $redirect_url
     * @return $this
     */
    public function secure(array $roles = ['*'], $redirect_url = null)
    {
        $this->secure['roles'] = $roles;
        $this->secure['redirect_url'] = $redirect_url;

        return $this;
    }

    public function accessBinds(array $bindsRules = [])
    {
        foreach ($bindsRules as $key => $bindsRule) {
            $this->secureBind($key, ...$bindsRule);
        }

        return $this;
    }

    protected function secureBind(string $bind, array $roles = ['*'], $redirect_url = null)
    {
        $this->secure['binds'][$bind]['roles'] = $roles;
        $this->secure['binds'][$bind]['redirect_url'] = $redirect_url;

        return $this;
    }



    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null|\Illuminate\Database\Eloquent\SoftDeletes
     *
     * @throws HttpException
     */
    protected function findModel($id)
    {
        if ($model = $this->app->model($this->modelName)->find($id)) {
            return $model;
        }
        return $this->app->abort(404, 'The requested record does not exist.');
    }
}