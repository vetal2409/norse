<?php

namespace e1\site\controllers;

use Silex;
use \e1\Application;
use e1\traits\controllerCore;
use e1\providers\Eloquent\Model;

use Symfony\Component\HttpFoundation\Request;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\MassAssignmentException;

class activeCRUD implements Silex\Api\ControllerProviderInterface
{
    use controllerCore {
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

    /**
     * Register route action(s)
     *
     * @param Silex\Application $app
     * @return mixed|Silex\ControllerCollection
     */
    public function connect(Silex\Application $app)
    {
        $converter = $app['сonverter'];
        $controllers = $this->connectTrait($app);

        $controllers->get('/index', [$this, 'index'])
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName, 'required' => false]))
            ->bind($this->modelName . '.index');

        $controllers->get('/view/{id}', [$this, 'view'])
            ->assert('id', '[^/]+')
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName]))
            ->bind($this->modelName . '.view');

        $controllers->patch('/{id}', [$this, 'restore'])
            ->assert('id', '[^/]+')
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName]))
            ->bind($this->modelName . '.restore');

        $controllers->delete('/{id}', [$this, 'delete'])
            ->assert('id', '[^/]+')
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName]))
            ->bind($this->modelName . '.delete');

        $controllers->post('/', [$this, 'upsert'])
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName, 'required' => false]))
            ->bind($this->modelName . '.create');

        $controllers->put('/{id}', [$this, 'upsert'])
            ->assert('id', '[^/]+')
            ->convert('model', $converter->get('model', ['modelName' => $this->modelName]))
            ->bind($this->modelName . '.update');

        return $controllers;
    }

    /**
     * Show model list items by $this->modelName
     *
     * @param Model|Builder $model
     * @param Application $app
     * @param Request $request
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function index($model, Application $app, Request $request)
    {
        $data = $request->query->all();
        $limit = (int)$request->request->get('per_page', 10);
        $current_page = (int)$request->query->get('current_page', 1);

        # search
        $classNameSearch = "\\e1\\models\\search\\{$this->modelName}Search";
        if (class_exists($classNameSearch)) {
            $modelSearch = new $classNameSearch;

            if ($modelSearch->fill($data)->validate()) {
                $model = $modelSearch->search();
            }
        }

        if (isset($this->additions['relation.list'])) {
            $model->with($this->additions['relation.list']);
        }

        /** @var LengthAwarePaginator $pager */
        $pager = $model->paginate($limit, ['*'], 'current_page', $current_page);
        $pager->setPath($app->url($this->modelName . '.index'));

        foreach ($data as $key => $value) {
            $pager->appends($key, $value);
        }

        # add append to models
        foreach ($pager->items() as $item) {
            /** @var Model|Builder $item # add appends */
            $item->append($this->additions['append.list'] ?? []);
        }

        return $this->render('index', [
            'pager' => $pager->toArray()
        ]);
    }


    /**
     * Create|Update model
     *
     * @param Model $model
     * @param Application $app
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws MassAssignmentException
     * @throws ValidationException
     */
    public function upsert($model, Application $app, Request $request)
    {
        $data = $request->request->all();
        unset($data['_method'], $data['_id']);

        if (!$data || !$model->validate($data)) {

            # add appends
            $model->append($this->additions['append.upsert'] ?? []);

            # add relations
            $model->load($this->additions['relation.upsert'] ?? []);

            return $this->render($model->getKey() ? 'update' : 'create', [
                'model' => $model->toArray(true),
            ]);
        }

        if ($model->save()) {
            list($type, $message) = ['success', 'Record successfully saved.'];
        } else {
            list($type, $message) = ['danger', 'Record not saved, something went wrong.'];
        }

        if (isset($app['session'])) {
            # Set Flash Message
            $app['session']->getFlashBag()->add($type, $message);
        }
        return $app->redirect($app->url($this->modelName . '.view', ['id' => $model->getKey()]));
    }

    /**
     * View model by id
     *
     * @param Model $model
     * @param Application $app
     * @param Request $request
     * @return string
     */
    public function view($model, Application $app, Request $request)
    {
        # add appends
        $model->append($this->additions['append.view'] ?? []);

        # add relations
        $model->load($this->additions['relation.view'] ?? []);

        return $this->render('view', [
            'model' => $model->toArray()
        ]);
    }

    /**
     * Delete model by id
     *
     * @param Model $model
     * @param Application $app
     * @param Request $request
     * @return string
     *
     * @throws \Exception
     */
    public function delete($model, Application $app, Request $request)
    {
        if ($model->delete()) {
            return $app->redirect($app->url($this->modelName . '.index'));
        }
        return $app->abort(500, 'Something went wrong.');
    }

    /**
     * Restore model by id
     *
     * @param Model|SoftDeletes $model
     * @param Application $app
     * @param Request $request
     * @return string
     */
    public function restore($model, Application $app, Request $request)
    {
        if ($model->restore()) {
            return $app->redirect($app->url($this->modelName . '.index'));
        }
        return $app->abort(500, 'Something went wrong.');
    }
}

