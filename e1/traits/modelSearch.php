<?php

namespace e1\traits;

use e1\Application;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

/**
 * Class modelSearch
 * @package e1\traits
 */
trait modelSearch
{

    public function addFilter(string $name, string $function)
    {
        $attribute = $this->getAttribute($name);

        if (!empty($attribute)) {
            $params = func_get_args();
            unset($params[1]);

            $this->query = call_user_func_array([$this->query, $function], $params);
        }
    }

    public function addCustomFilter(string $name, string $function, ...$params)
    {
        $attribute = $this->getAttribute($name);

        if (!empty($attribute)) {

            $this->query = call_user_func_array([$this->query, $function], $params);
        }
    }
}

