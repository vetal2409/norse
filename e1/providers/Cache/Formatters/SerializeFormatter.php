<?php

namespace e1\providers\Cache\Formatters;

use e1\providers\Cache\Interfaces\FormatterInterface;

class SerializeFormatter implements FormatterInterface
{
    public function setFormat(array $record){
        return @serialize($record);
    }

    public function getFormat(string $record){
        return @unserialize($record, ['allowed_classes' => true]);
    }
}