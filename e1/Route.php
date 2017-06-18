<?php

namespace e1;

use e1\Application as Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Route
 * @package e1
 */
class Route extends \Silex\Route
{
    /**
     * @param array $roles
     * @param string $redirect_url
     * @param string $security_checker
     * @return $this
     */
    public function secure($roles, $redirect_url = null, string $security_checker)
    {
        $this->before(function (Request $request, Application $app) use ($roles, $redirect_url, $security_checker) {

            if (isset($app[$security_checker]) && $app[$security_checker]->isGranted($roles, $request)) {
                return;
            }
            if (empty($redirect_url)) {
                $app->abort(403);
            }
            return $app->redirect($app->url($redirect_url));
        }, Application::LATE_EVENT);

        $this->after(function (Request $request, Response $response, Application $app) use ($roles, $redirect_url, $security_checker) {
            if (isset($app[$security_checker])) {
                $app[$security_checker]->storeUserCredentials($request, $response);
            }
        }, Application::EARLY_EVENT);
        return $this;
    }
}
