<?php

namespace e1\traits;

use e1\Application;
use Silex;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class coreController
 * @package e1\Controllers
 *
 * @property \e1\Application $app
 * @property array $secure
 *
 * @property string $modelName
 * @property string $layoutTemplate
 * @property string|null $pathTemplate
 * @property string $security_checker
 */
trait controllerCore
{
    protected $app;
    protected $secure;
    protected $security_checker;

    protected $layoutTemplate;
    protected $pathTemplate;
    public $modelName;

    /**
     * coreCRUD constructor.
     *
     * @param string $layoutTemplate
     * @param string $security_checker
     * @param string $pathTemplate
     *
     * @internal param string $security
     */
    public function __construct(string $layoutTemplate, string $security_checker = 'security.authorization_checker', string $pathTemplate = '')
    {
        $this->layoutTemplate = $layoutTemplate;

        $this->security_checker = $security_checker;
        $this->modelName = substr(strrchr(get_called_class(), "\\"), 1);
        $this->pathTemplate = empty($pathTemplate) ? "widget/$this->modelName" : $pathTemplate;
    }

    public function connect(Silex\Application $app)
    {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        if ($this->secure) {
            $controllers->secure($this->secure['roles'], $this->secure['redirect_url'], $this->security_checker);
        }

        $controllers->before(function (Request $request, Application $app) use ($app, $controllers) {

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
     * @param string|null $redirect_url
     * @return $this
     */
    public function secure(array $roles = [], $redirect_url = null)
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
     * Renders a template.
     *
     * @param string $template The template name
     * @param array $data An array of parameters to pass to the template
     * @param string $ext
     *
     * @return string The rendered template
     */
    public function render(string $template, array $data = [], string $ext = 'twig'): string
    {
        # todo: or move to App!!!
        $format = $this->app['request_stack']->getCurrentRequest()->getRequestFormat();

        return $this->app->render("$this->pathTemplate/$template.$format.$ext", [
            'layout' => "$this->layoutTemplate.$format.$ext",
            'modelName' => $this->modelName,
            'data' => $data
        ]);
    }

    /**
     * @param string|null $id
     * @param bool $required
     * @return \Illuminate\Database\Eloquent\Model|null|\Illuminate\Database\Eloquent\SoftDeletes
     */
    protected function findModel($id = null, $required = true)
    {
        $model = $this->app->model($this->modelName);

        if ($id && $model = $model->find($id)) {
            return $model;
        } elseif ($required) {
            return $this->app->abort(404, 'The requested record does not exist.');
        }
        return $model;
    }
}