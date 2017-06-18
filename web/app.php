<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

#FIXES
/**
 * see shouldUseCollections() [/jenssegers/mongodb/src/Jenssegers/Mongodb/Query/Builder]
 * f-style code (after Eloquent 5.3 Query Builder Returns a Collection)
 */
function app()
{
    return new class()
    {
        public function version()
        {
            return '5.4';
        }
    };
}

use e1\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new e1\Application();

$app['base.dir'] = __DIR__;

# MIDDLEWARE

$app->error(function (\Exception $e, Request $request, $code) use ($app) {

    $message = $app['debug'] ? $e->getMessage() : $app['translator']->get("error.$code", [], $app['locale']);
    $data = ['code' => $code, 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $message];

    # Token
    $user = $app['security.user']  ?? null;
    $token = $user ? $user->token : null;

    if ($app['negotiator']->isFormat('Accept', 'application/json')) {
        return $app->json(['success' => false, 'data' => $data, 'errors' => $message, 'token' => $token], $code);
    }

    $format = $request->getRequestFormat();

    $templates = [
        'errors/' . $code . ".$format.twig",
        'errors/' . substr($code, 0, 2) . "x.$format.twig",
        'errors/' . $code[0] . "xx.$format.twig",
        "errors/default.$format.twig",
    ];

    return new Response($app['twig']->resolveTemplate($templates)->render($data), $code);
});

$app->before(function (Request $request, \e1\Application $app) {

    if ($app['negotiator']->isFormat('Accept', 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : []);
    }

    if (0 === strpos($request->headers->get('Content-Type'), 'multipart/form-data')) {
        $request->request->replace($_POST);
    }
});

$app->view(function ($result, Request $request) use ($app) {

    # default response status
    $status = 200;

    # Error
    $error = null;
    if (is_bool($result)) {
        if (!$result) {
            list($status, $error) = [400, 'Failed.'];
        }
        $result = [];
    } elseif (is_array($result) && array_key_exists('errors', $result) && !empty($result['errors'])) {
        list($status, $error) = [422, 'Validate failed.'];
    }

    # Token # todo: refactor RBAC!
    $user = $app['security.user']  ?? null;
    $token = $user ? $user->token : null;

    if ($app['negotiator']->isFormat('Accept', 'application/json')) {
        return $app->json(['success' => $error === null, 'data' => $result, 'errors' => $error, 'token' => $token], $status);
    }

    if ($app['negotiator']->isFormat('Accept', 'text/html')) {
        return $result;
    }

    return $result;
});

# REGISTER SERVICE PROVIDERS

$app->register(new Silex\Provider\SessionServiceProvider()); # session
$app->register(new e1\providers\Predis\PredisServiceProvider()); # Predis
$app->register(new Silex\Provider\LocaleServiceProvider()); # Symfony Locale
$app->register(new \e1\providers\Twig\TwigServiceProvider()); # Twig
$app->register(new \e1\providers\Negotiator\NegotiatorServiceProvider()); # Negotiator
$app->register(new Silex\Provider\AssetServiceProvider()); # Asset
$app->register(new \e1\providers\Convert\ConverterServiceProvider()); # Convert
$app->register(new \e1\providers\SimplePie\SimplePieServiceProvider()); # Convert
$app->register(new \e1\providers\Eloquent\EloquentMongoDbServiceProvider()); # Eloquent MongoDb
$app->register(new \e1\providers\illuminateValidation\ValidationServiceProvider()); # Laravel Validate
$app->register(new \e1\providers\illuminateTranslation\TranslationServiceProvider()); # Laravel Translation

$app->register(new e1\providers\RBAC\tokenServiceProvider('api.security.authorization_checker')); # RBAC TOKEN
$app->register(new e1\providers\RBAC\sessionServiceProvider('session.security.authorization_checker')); # RBAC SESSION
$app->register(new e1\providers\RBAC\JWTServiceProvider('jwt.security.authorization_checker')); # RBAC SESSION

# Extend

$app->extend('twig', function ($twig, $app) {
    $twig->addGlobal('user', $app['security.user'] ?? null);
    $twig->addGlobal('breadcrumbs', ['name' => '', 'min' => '']);
    return $twig;
});


# SERVICES

$app->register(new \e1\services\entity\user());
$app->register(new \e1\services\entity\feed());
$app->register(new \e1\services\entity\channel());

# CONTROLLERS

# API V1
$app->mount('{_locale}/v1/auth', new \e1\api\v1\controllers\auth('api.security.authorization_checker'));
$app->mount('{_locale}/v1/feed', new \e1\api\v1\controllers\feed('api.security.authorization_checker'));
$app->mount('{_locale}/v1/user', new \e1\api\v1\controllers\user('api.security.authorization_checker'));
$app->mount('{_locale}/v1/channel', new \e1\api\v1\controllers\channel('api.security.authorization_checker'));

# SITE
$app->mount('{_locale}/', new \e1\site\controllers\auth('layout/auth', 'site.dashboard', 'auth.login', 'session.security.authorization_checker'));
$app->mount('{_locale}/', (new \e1\site\controllers\site('layout/main', 'session.security.authorization_checker'))->secure(['*']));
$app->mount('{_locale}/user', (new \e1\site\controllers\user('layout/main', 'session.security.authorization_checker'))->secure([\e1\models\user::ROLE_ADMIN]));
$app->mount('{_locale}/feed', (new \e1\site\controllers\feed('layout/main', 'session.security.authorization_checker'))->secure([\e1\models\user::ROLE_ADMIN]));
$app->mount('{_locale}/channel', (new \e1\site\controllers\channel('layout/main', 'session.security.authorization_checker'))->secure(['*']));

# ACTIONS
$app->get('/dev/swagger-api-doc/{version}/', function ($version, Application $app) {
    error_reporting(0);
    return \Swagger\scan(dirname($app['base.dir']) . "/e1/api/$version/swagger-doc", []);
});

Request::enableHttpMethodParameterOverride();
return $app;

