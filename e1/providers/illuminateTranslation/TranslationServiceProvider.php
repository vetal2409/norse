<?php

namespace e1\providers\illuminateTranslation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class TranslationServiceProvider
 * @package e1\providers\illuminateTranslation
 *
 * @property \e1\Application $app
 * @property string $locale
 * @property string $fallback_locale
 *
 * @REQUIRE
 *
 * @REGISTER:
 *
 * @USAGE:
 *
 */
class TranslationServiceProvider implements ServiceProviderInterface
{
    protected $app;
    protected $fallback_locale = 'en';

    public function register(Container $app)
    {
        $this->app = $app;

        $app['translation.loader'] = function () use ($app) {
            return new FileLoader(new Filesystem(), $app['translator.path'] ?? '');
        };

        $app['translator'] = function () use ($app) {

            if (isset($app['translation.fallback_locale'])) {
                $this->fallback_locale = $app['translation.fallback_locale'];
            }

            $trans = new Translator($app['translation.loader'], $app['locale']);
            $trans->setFallback($this->fallback_locale);
            return $trans;
        };
    }
}
