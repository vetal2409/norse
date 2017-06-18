<?php

namespace e1\providers\Convert;

use e1\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ConverterCore
 * @package e1\providers\Convert
 *
 * @property \e1\Application $app
 */
abstract class ConverterCore
{
    protected $app;

    public function __construct(Application $app, array $params = [])
    {
        $this->app = $app;
        foreach ($params as $name => $param) {
            $this->$name = $param;
        }
    }

    abstract public static function converterName(): string;

    abstract public function convert($attribute = null, Request $request);
}