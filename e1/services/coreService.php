<?php

namespace e1\services;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class coreService
 * @package e1\services
 *
 * @property \e1\Application $app
 * @property string $serviceName
 */
class coreService implements ServiceProviderInterface
{
    protected $app;
    protected $serviceName;

    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $this->app = $app;
        $app["service.$this->serviceName"] = $this;
    }

    /**
     * coreService constructor.
     * @param null|string $serviceName
     */
    public function __construct($serviceName = null)
    {
        $this->serviceName = $serviceName ?? substr(strrchr(get_called_class(), "\\"), 1);
    }
}