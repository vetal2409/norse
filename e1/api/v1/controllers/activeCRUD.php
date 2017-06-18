<?php

namespace e1\api\v1\controllers;

use \e1\Application;
use e1\traits\controllerApi;
use e1\providers\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base CRUD controller.
 *
 * Class activeCRUD
 * @package e1\api\v1\controllers
 */
class activeCRUD implements ControllerProviderInterface
{
    use controllerApi {
        connect as public connectTrait;
    }

    /**
     * For add appends and relations in need default action
     *
     * @var array
     */
    public $additions = [

        # appends
        'append.view' => [],
        'append.upsert' => [],
        'append.list' => [],

        # relations
        'relation.view' => [],
        'relation.upsert' => [],
        'relation.list' => [],
    ];

    public function connect(\Silex\Application $app)
    {
        $converter = $app['сonverter'];
        $controllers = $this->connectTrait($app);

        $controllers->post('/list', [$this, 'index'])
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName, 'required' => false]))
            ->bind("$this->base_bind.index");

        $controllers->get('/{id}', [$this, 'view'])
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName]))
            ->assert('id', '[^/]+')
            ->bind("$this->base_bind.view");

        $controllers->patch('/{id}', [$this, 'restore'])
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName]))
            ->assert('id', '[^/]+')
            ->bind("$this->base_bind.restore");

        $controllers->delete('/{id}', [$this, 'delete'])
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName]))
            ->assert('id', '[^/]+')
            ->bind("$this->base_bind.delete");

        $controllers->post('/', [$this, 'upsert'])
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName, 'required' => false]))
            ->bind("$this->base_bind.create");

        $controllers->put('/{id}', [$this, 'upsert'])
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName]))
            ->bind("$this->base_bind.update");

        return $controllers;
    }

    /**
     * @param Model|Builder $model
     * @param Application $app
     * @param Request $request
     * @return array
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \InvalidArgumentException
     */
    public function index(Model $model, Application $app, Request $request)
    {
        $data = $request->request->all();
        $limit = (int)$request->request->get('per_page', 10);
        $current_page = (int)$request->request->get('current_page', 1);

        # search
        $classNameSearch = "\\e1\\models\\search\\{$this->modelName}Search";
        if (class_exists($classNameSearch)) {
            $modelSearch = new $classNameSearch;

            if ($modelSearch->validate($data)) {
                $model = $modelSearch->search();
            } else {
                return $modelSearch->toArray(true);
            }
        }

        if (isset($this->additions['relation.list'])) {
            $model->with($this->additions['relation.list']);
        }

        /** @var LengthAwarePaginator $pager */
        $pager = $model->paginate($limit, ['*'], 'current_page', $current_page);
        $pager->setPath($app->url($this->base_bind.'.index'));

        foreach ($data as $key => $value) {
            $pager->appends($key, $value);
        }

        # add append to models
        foreach ($pager->items() as $item) {
            /** @var Model|Builder $item # add appends */
            $item->append($this->additions['append.list'] ?? []);
        }

        return $pager->toArray();
    }

    /**
     * @param Model|Builder $model
     * @param Application $app
     * @param Request $request
     * @return mixed
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function upsert(Model $model, Application $app, Request $request)
    {
        /** @var array $data */
        $data = $request->request->all();

        if (!empty($data['_scenario'])) {
            $model->setScenario($data['_scenario']);
        } else {
            $model->setScenario($model->exists ? 'update' : 'create');
        }

        unset($data['_scenario'], $data['_method']);

        # add appends
        $model->append($this->additions['append.upsert'] ?? []);

        # add relations
        $model->load($this->additions['relation.upsert'] ?? []);

        if ($model->validate($data)) {

            if (!$app['security.user']->can("$this->modelName.$model->_scenario", $model)) {
                return $app->abort(403);
            }

            if ($model->save()) {
                return $model->toArray();
            }
        }

        return $model->toArray(true);
    }

    /**
     * @param Model|Builder $model
     * @param Application $app
     * @param Request $request
     * @return mixed
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function view(Model $model, Application $app, Request $request)
    {
        $model->setScenario('view');

        if (!$app['security.user']->can("$this->modelName.read", $model)) {
            return $app->abort(403);
        }

        # add appends
        $model->append($this->additions['append.view'] ?? []);

        # add relations
        $model->load($this->additions['relation.view'] ?? []);

        return $model->toArray();
    }

    /**
     * @param Model|Builder $model
     * @param Application $app
     * @param Request $request
     * @return mixed
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function delete(Model $model, Application $app, Request $request)
    {
        if (!$app['security.user']->can("$this->modelName.delete", $model)) {
            return $app->abort(403);
        }

        if ($model->setScenario('delete')->delete()) {
            return true;
        }
        return $app->abort(500);
    }

    /**
     * @param Model|Builder $model
     * @param Application $app
     * @param Request $request
     * @return mixed
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function restore(Model $model, Application $app, Request $request)
    {
        if (!$app['security.user']->can("$this->modelName.restore", $model)) {
            return $app->abort(403);
        }

        if ($model->setScenario('restore')->restore()) {
            return true;
        }
        return $app->abort(500);
    }
}
