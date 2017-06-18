<?php

namespace e1;

use Silex\Application\UrlGeneratorTrait;

class Application extends \Silex\Application
{
    use UrlGeneratorTrait;

    public function __construct(array $values = array())
    {
        parent::__construct($values);
        $this['route_class'] = Route::class;
    }

    /**
     * @param string $name
     * @return \Jenssegers\Mongodb\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function model($name)
    {
        return $this['model']($name);
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    public function request()
    {
        return $this['request_stack']->getCurrentRequest();
    }

    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public function render($template = null, array $data = null)
    {
        return $this['twig']->render($template, $data);
    }
}

