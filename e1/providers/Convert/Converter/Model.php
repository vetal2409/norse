<?php

namespace e1\providers\Convert\Converter;

use e1\providers\Convert\ConverterCore;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Model
 * @package e1\providers\Сonvert\Converter
 *
 * @property \e1\Application $app
 * @property string $modelName
 * @property string $idName
 * @property bool $required
 */
class Model extends ConverterCore
{
    protected $modelName;
    protected $idName = 'id';
    protected $required = true;

    public function convert($attribute = null, Request $request)
    {
        $id = $request->get($this->idName);
        $model = $this->app->model($this->modelName);

        if ($id && $modelById = $model->find($id)) {
            return $modelById;
        } elseif ($this->required) {
            return $this->app->abort(404);
        }
        return $model;
    }

    public static function converterName(): string
    {
        return 'model';
    }
}