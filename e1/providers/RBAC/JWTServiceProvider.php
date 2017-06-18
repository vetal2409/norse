<?php

namespace e1\providers\RBAC;

use e1\models\user;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use e1\providers\RBAC\Interfaces\AuthorizationCheckerInterface;


/**
 * Class JWTServiceProvider
 * @package e1\Providers\Auth
 *
 * JSON Web Tokens
 *
 * @doc Service provide simple JWT auth process
 *
 * @PARAMS:
 * $app['security.jwt.attr.exp'] - time while token valid
 * $app['jwt.security.key'] - secret key for 3th path of token
 *
 * @REGISTER:
 * $app->register(new e1\providers\RBAC\JWTServiceProvider('jwt.security.authorization_checker'));
 *
 * @USAGE:
 * $app['jwt.security.authorization_checker']->newKey(array $payload, int $exp = null) - create new JWT token
 *
 * @property \e1\Application $app
 */
class JWTServiceProvider extends security implements ServiceProviderInterface
{
    //const HEADER = ['alg' => 'HS256', 'typ' => 'JWT'];
    const HEADER_BASE64 = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ91';
    const PROTECTED_PAYLOAD = ['iat', 'jti', 'iss', 'nbf', 'exp', 'sub', 'aud'];


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

    /** Load data to $app['security.user']
     * @param Request $request
     * @return bool
     */
    public function loadUserCredentials(Request $request): bool
    {
        # header:    Authorization: Bearer 'token'
        $jwtData = str_replace('Bearer ', '', $request->headers->get('Authorization'));

        # isSet jwt
        if (empty($jwtData)) {
            $this->app->abort(403, 'The JWT is absent');
        }
        $jwt = explode('.', $jwtData);

        if (count($jwt) != 3) {
            $this->app->abort(403, 'The JWT incorrect');
        }

        # check signature
        if ($jwt[2] !== $this->sign($jwt[0] . '.' . $jwt[1])) {
            $this->app->abort(403, 'The JWT incorrect signature');
        }

        # decode
        $data = $this->jsonDecode($this->base64UrlDecode($jwt[1]));

        # check TTL of JWT
        if ($data->exp < time()) {
            $this->app->abort(403, 'Time of the JWT is over');
        }

        //set
        $this->app['security.user'] = $this->app->model('user')->setRawAttributes((array) $data);

        return true;
    }


    /** Create new JWT token
     *
     * @param array $payload
     * @param int|null $exp
     * @return string
     * @throws \RuntimeException
     */
    public function newKey(array $payload, int $exp = null)
    {
        $time = time();
        //TODO: array filter or intersect with PROTECTED_PAYLOAD
        $merged_payload = array_merge([
                'iat' => $time,
                'exp' => $time + $exp ?? $this->app['jwt.security.attr.exp'],
            ], $payload);
        $header = $this->getEncodeHeader();
        $data = $this->base64UrlEncode($this->jsonEncode($merged_payload));
        $sign = $this->sign($header . '.' . $data);
        return $header . '.' . $data . '.' . $sign;
    }

    /**
     * generate default header
     * @return string base64 default header
     */
    protected function getEncodeHeader()
    {
        return self::HEADER_BASE64;
    }

    /**
     * create new sing
     * @param string $data
     * @return string
     */
    public function sign($data/* , $alg = 'HS256' */)
    {
        //TODO: use $alg param
        return $this->base64UrlEncode(
            mhash('MHASH_SHA256', $data, $this->app['jwt.security.key'])
        );
    }

    /**
     * Encodes to JSON, validating the errors
     *
     * @param mixed $data
     * @return string
     *
     * @throws \RuntimeException When something goes wrong while encoding
     */
    public function jsonEncode($data)
    {
        $json = json_encode($data);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Error while encoding to JSON: ' . json_last_error_msg());
        }

        return $json;
    }

    /**
     * Encodes to base64url
     *
     * @param string $data
     * @return string
     */
    public function base64UrlEncode($data)
    {
        return str_replace('=', '', strtr(base64_encode($data), '+/', '-_'));
    }

    /**
     * Decodes from JSON, validating the errors (will return an associative array
     * instead of objects)
     *
     * @param string $json
     * @return mixed
     *
     * @throws \RuntimeException When something goes wrong while decoding
     */
    public function jsonDecode($json)
    {
        $data = json_decode($json);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Error while decoding to JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Decodes from base64url
     *
     * @param string $base64Data
     * @return string
     */
    public function base64UrlDecode($base64Data)
    {
        if ($remainder = strlen($base64Data) % 4) {
            $base64Data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($base64Data, '-_', '+/'));
    }
}
