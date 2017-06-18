<?php

namespace e1\providers\Cache;

use e1\providers\Cache\Formatters\SerializeFormatter;
use e1\providers\Cache\Interfaces\FormatterInterface;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Cache
 * @package e1\providers\Telegram\State
 *
 * @property \e1\Application $app
 * @property FormatterInterface $formatter
 * @property string $prefix
 */
abstract class Cache
{
    protected $app;
    protected $prefix;
    protected $formatter;

    /**
     * Set record(s) from storage by key.
     *
     * @param string $key
     * @param mixed $collection
     * @param float|int $minutes
     * @return void
     */
    abstract public function set(string $key, $collection, $minutes);

    /**
     * Get record(s) from storage by key.
     *
     * @param string $key Key storage
     * @param null $default Default value
     * @param bool $formatter Formatting record(s)
     * @return mixed
     */
    abstract public function get(string $key, $default = null, $formatter = true);

    /**
     * Check has in storage.
     *
     * @param string $key
     * @return bool
     */
    abstract public function has(string $key): bool;

    /**
     * Remove cache by key name.
     *
     * @param string $key
     * @return bool
     */
    abstract public function remove(string $key): bool;

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed $value
     * @return int|bool
     */
    abstract public function increment($key, $value = 1);

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed $value
     * @return int|bool
     */
    abstract public function decrement($key, $value = 1);

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    abstract public function flush();


    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }
        return $this->formatter;
    }

    /**
     * Gets the default formatter.
     *
     * @return SerializeFormatter
     */
    protected function getDefaultFormatter()
    {
        return new SerializeFormatter();
    }

    /**
     * Get prefix storage
     */
    public function getPrefix()
    {
        $this->prefix;
    }
}