<?php

namespace e1\models;

use Carbon\Carbon;
use e1\Providers\Eloquent\Model;
use e1\providers\RBAC\Interfaces\IdentityInterface;
use Illuminate\Validation\Rule;

/**
 * Class user
 * @package e1\Models
 *
 * @property string $_id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $email
 * @property string $role
 * @property string $password
 * @property string $old_password
 * @property string $password_confirmation
 *
 * @property string $password_hash
 * @property string $password_reset_token
 *
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class user extends Model implements IdentityInterface
{
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_REGISTRATION = 'registration';
    const SCENARIO_PASSWORD_CHANGE = 'password_change';

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
        'logged_at',
        'birthday'
    ];

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'role',

        'password',
        'old_password',
        'password_confirmation',

        'logged_at', # on login this field update
        'terms'
    ];

    protected $hidden = [
        'password_hash',
        'password_reset_token',
    ];

    protected $forgotten = [
        'password_confirmation',
        'old_password',
        'code',
    ];

    protected $defaultValues = [
        'role' => self::ROLE_USER,
    ];

    protected $scenarioRules = [

        self::SCENARIO_CREATE => [
            'first_name' => 'required',
            'middle_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',

            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ],

        self::SCENARIO_UPDATE => [
            'first_name' => 'required',
            'middle_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
        ],

        self::SCENARIO_PASSWORD_CHANGE => [
            'old_password' => 'required|isValidPassword',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ],

        self::SCENARIO_REGISTRATION => [
            'first_name' => 'required',
            'middle_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'terms' => 'required',

            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ],

        self::SCENARIO_LOGIN => [
            'phone' => 'required',
            'password' => 'required|isValidPassword',
        ]
    ];

    public function rules(): array
    {
        return [
            'first_name' => 'string|max:255',
            'middle_name' => 'string|max:255',
            'last_name' => 'string|max:255',

            'email' => "email|unique:users,email,{$this->getKey()},_id",
            'role' => [Rule::in([self::ROLE_ADMIN, self::ROLE_USER])],

            'password' => 'string',
            'old_password' => 'string',
            'password_confirmation' => 'string',
        ];
    }

    public function custom()
    {
        # is valid with password hash
        $this->addRule('isValidPassword', function ($attribute, $value, $parameters) {
            return $this->validatePassword($value);
        }, ':attribute is`t valid.');
    }


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            /** @var $model self */

            if ($password = $model->getAttribute('password')) {
                $model->setPassword($password);
            }

            if ($model->isScenario([self::SCENARIO_PASSWORD_CHANGE])) {
                $model->generatePasswordResetToken();
            }

            if ($model->isScenario([self::SCENARIO_REGISTRATION, self::SCENARIO_CREATE])) {
                $model->generatePasswordResetToken();
                $model->generateAuthKey();
            }

            $model->forget('password', 'terms');

            return true;
        });


        static::deleting(function ($model) {
            /** @var $model self */
            # todo: !!!
            return true;
        });
    }

    # appends

    public function getChannelsAttribute()
    {
        return $this->app()->model('agenda')->whereIn('user_ids', [$this->getKey()])->get();
    }


    # identity interface

    public function validatePassword($password): bool
    {
        return $this->app('security.authorization_checker')->validatePassword($password, $this->getAttribute('password_hash', ''));
    }

    public function setPassword($password)
    {
        $this->setAttribute('password_hash', $this->app('security.authorization_checker')->generatePasswordHash($password));
    }

    public function isRole(array $roles): bool
    {
        return in_array($this->getAttribute('role'), $roles, false);
    }

    public function generateAuthKey()
    {
        $this->setAttribute('token', $this->app('security.authorization_checker')->generateRandomString());
    }

    public function generatePasswordResetToken()
    {
        $this->setAttribute('password_reset_token', $this->app('security.authorization_checker')->generatePasswordResetToken());
    }
}
