<?php

namespace e1\providers\Eloquent;

use e1\traits\modelRestriction;
use e1\traits\modelSoft;
use e1\traits\modelValidate;

use Illuminate\Support\Str;
use Illuminate\Events\Dispatcher;
use Jenssegers\Mongodb\Connection;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Illuminate\Database\Eloquent\Scope;
use Silex\Api\BootableProviderInterface;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Capsule\Manager as Capsule;
use Silex\Application;

class Model extends Eloquent implements Scope
{
    use modelSoft, modelRestriction, modelValidate;

    const SCENARIO_UPDATE = 'update';
    const SCENARIO_CREATE = 'create';

    protected $guarded = [];

    # fillable fields that won't be saved to database
    protected $forgotten = [];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected function getDateFormat()
    {
        return $this->dateFormat ?? 'U'; # 'Y-m-d H:i:s';
    }

    protected static function boot()
    {
        parent::boot();
    }

    public function save(array $options = [])
    {
        $user = $this->getAuthUser();

        if ($this->exists) {
            $this->setAttribute($this->updatedByColumn(), $user ? $user->_id : null);
        } else {
            $this->setAttribute($this->createdByColumn(), $user ? $user->_id : null);
        }

        # forget fields
        foreach ($this->forgotten as $forgotten) {
            $this->forget($forgotten);
        }

        return parent::save($options);
    }
}

class EloquentMongoDbServiceProvider implements BootableProviderInterface, ServiceProviderInterface
{
    public function boot(Application $app)
    {
        $app['db.init'];
    }

    public function register(Container $app)
    {
        $app['db.init'] = function () use ($app) {
            $capsule = new Capsule;
            $capsule->getDatabaseManager()->extend('mongodb', function ($config) {
                return new Connection($config);
            });
            $capsule->addConnection($app['db.config']);
            $capsule->setAsGlobal();
            $capsule->setEventDispatcher(new Dispatcher());
            $capsule->bootEloquent();

            Eloquent::addGlobalScope('app', function () use ($app) {
                return $app;
            });

            $app['db.capsule'] = $capsule;
        };

        $app['model'] = $app->protect(function ($name) use ($app) {
            $class_name = '\\e1\\models\\' . $name;

            if (class_exists($class_name)) {
                $model = new $class_name();
            } else {
                $model = new Model();
            }

            $e_name = null;
            if (defined("$class_name::COLLECTION_NAME")) {
                $e_name = constant("$class_name::COLLECTION_NAME");
            } else {
                $e_name = str_replace('\\', '', Str::snake(Str::plural(class_basename($class_name))));
            }
            $model->setTable($e_name);

            return $model;
        });
    }
}