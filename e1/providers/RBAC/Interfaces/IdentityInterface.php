<?php

namespace e1\providers\RBAC\Interfaces;

interface IdentityInterface
{

    public function validatePassword($password):bool;

    public function setPassword($password);

    public function generateAuthKey();

    public function generatePasswordResetToken();

    public function isRole(array $roles): bool;

}