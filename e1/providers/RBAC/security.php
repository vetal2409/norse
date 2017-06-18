<?php

namespace e1\providers\RBAC;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class security
 * @package e1\providers\RBAC
 *
 * @property \e1\Application $app
 * @property string $name_container
 */
abstract class security
{
    protected $app;
    protected $name_container;

    public function __construct(string $name_container = 'security.authorization_checker')
    {
        $this->name_container = $name_container;
    }

    public abstract function isGranted(array $roles = [], Request $request): bool;

    public abstract function storeUserCredentials(Request $request, Response $response): bool;

    public abstract function loadUserCredentials(Request $request): bool;

    /**
     * Verifies a password against a hash.
     *
     * @param string $password The password to verify.
     * @param string $hash The hash to verify the password against.
     * @return boolean whether the password is correct.
     * @throws \Exception
     */
    public function validatePassword($password, $hash): bool
    {

        if (!is_string($password) || $password === '') {
            throw new \Exception('Password must be a string and cannot be empty.');
        }

        if (!is_string($hash) || $hash === '') {
            throw new \Exception('Hash is invalid.');
        }

        return password_verify(trim($password), $hash);
    }

    /**
     * Generates a secure hash password.
     *
     * @param string $password The password to be hashed.
     * @param integer $cost Cost parameter.
     * @return string
     */
    public function generatePasswordHash($password, $cost = 10): string
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
    }

    /**
     * Generate str_time for verification.
     *
     * @return string
     */
    public function generatePasswordResetToken(): string
    {
        return $this->generateRandomString() . '_' . time();
    }

    /**
     * Verification Password Reset Token
     *
     * @param string $token
     * @return bool
     */
    public function isPasswordResetTokenValid(string $token): bool
    {

        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = $this->app['security.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @return string
     */
    public function generateRandomString(): string
    {
        # TODO: make safer!
        return uniqid('', true);
    }
}