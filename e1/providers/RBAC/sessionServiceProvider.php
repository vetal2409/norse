<?php

namespace e1\providers\RBAC;

use e1\models\user;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Session and cookie auth provider.
 *
 * provide app[site.security.authorization_checker] service for e1::Route::sercure
 * require: app[security.key] to sign cookie
 *
 * Use session var:
 * security.user - security user serialize object, where
 * app[security.user]->role - role name
 *
 * Use cookie name:
 * _su - security user (db user model id)
 * _sus - security user sign
 */

/**
 * Class sessionUserServiceProvider
 * @package e1\Providers\RBAC
 */
class sessionServiceProvider extends security implements ServiceProviderInterface
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
        /** @var user $user */
        $user = $this->app['security.user'] ?? null;

        if (!$request->hasSession()) {
            $request->setSession(new Session());
        }

        $session = $request->getSession();

        # clear
        if (!isset($user, $user->_id)) {
            $response->headers->clearCookie('_su');
            $response->headers->clearCookie('_sus');
            $session->clear();
            //$session->invalidate(); //to change PHPSID
            return false;
        }
        # from app to session
        $session->set('security.user', $user->getAttributes());

        # from app to cookies
        if (!isset($this->app['security.user.permanent'])) {
            return true;
        }
        $sign = sha1($user->getKey() . $this->app['security.key']);
        $response->headers->setCookie(new Cookie('_su', $user->getKey(), new \DateTimeImmutable('+5 year')));
        $response->headers->setCookie(new Cookie('_sus', $sign, new \DateTimeImmutable('+5 year')));

        return true;
    }

    public function loadUserCredentials(Request $request): bool
    {

        if (!$request->hasSession()) {
            $request->setSession(new Session());
        }

        $session = $request->getSession();
        # from session to app
        if ($session->has('security.user')) {
            /** @var user $user */
            $user = $this->app->model('user');
            $user->setRawAttributes($session->get('security.user'));

            $this->app['security.user'] = $user;
            return true;
        }
        if (!$request->cookies->has('_su')) {
            return false;
        }
        $id = $request->cookies->get('_su');
        $sign = $request->cookies->get('_sus');
        # check sign
        if (sha1($id . $this->app['security.key']) !== $sign) {
            return false;
        }
        /** @var user $user */
        $user = $this->app->model('user')->find($id);

        # if user is gone
        if (!isset($user, $user->_id)) {
            return false;
        }
        # from cookie to app and to session
        $this->app['security.user'] = $user;
        $session->set('security.user', $user->getAttributes());

        return true;
    }
}
