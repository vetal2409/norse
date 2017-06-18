<?php

namespace e1\traits;

use e1\models\user;
use e1\providers\RBAC\Interfaces\IdentityInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait modelRestriction
 * @package e1\traits
 */
trait modelRestriction
{
    public function apply(Builder $builder, Model $model)
    {
        /**
         * @var IdentityInterface|user $model
         * @var array $restriction
         */

        $model = $builder->getModel();
        $user = $model->getAuthUser();
        $restriction = $model->getModelRestrictionRole();

        if (isset($user, $restriction) && count(array_intersect((array)$user->role, (array)$restriction))) {
            $builder->where([$model->createdByColumn() => $user->getKey()]);
        }
    }

    public static function bootModelRestriction()
    {
        static::addGlobalScope(new self);
    }

    # Help functions

    /**
     * @return mixed
     */
    protected function getAuthUser()
    {
        if ($this->app('security.authorization_checker')) {
            return $this->app('security.user');
        }
        return [];
    }

    # Getters

    public function getModelRestrictionRole(): array
    {
        return $this->restriction ?? [];
    }

    public function createdByColumn(): string
    {
        return defined('static::CREATED_BY') ? static::CREATED_BY : 'created_by';
    }

    public function updatedByColumn(): string
    {
        return defined('static::CREATED_BY') ? static::UPDATED_BY : 'updated_by';
    }

    # Readers

    public function getCreatedByAttribute()
    {
        return $this->app()->model('user')->where('_id', '=', array_get($this->attributes, $this->createdByColumn(), ''))->first();
    }

    public function getUpdatedByAttribute()
    {
        return $this->app()->model('user')->where('_id', '=', array_get($this->attributes, $this->updatedByColumn(), ''))->first();
    }
}