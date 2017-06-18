<?php

namespace e1\providers\Cache\Interfaces;

/**
 * Interface FormatterInterface
 * @package e1\providers\Cache\Interfaces\CacheInterface
 */
interface FormatterInterface
{
    public function setFormat(array $record);

    public function getFormat(string $record);
}