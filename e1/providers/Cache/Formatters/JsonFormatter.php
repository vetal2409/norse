<?php

namespace e1\providers\Cache\Formatters;

use e1\providers\Cache\Interfaces\FormatterInterface;

class JsonFormatter implements FormatterInterface
{
    public function setFormat(array $record)
    {
        return json_encode($record);
    }

    public function getFormat(string $record)
    {
        return json_decode($record);
    }
}