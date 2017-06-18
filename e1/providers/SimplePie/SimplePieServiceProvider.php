<?php

namespace e1\providers\SimplePie;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

/**
 * Class SimplePie
 * @package e1\providers\SimplePie
 *
 * @property \e1\Application $app
 * @property \SimplePie $client
 *
 * @REQUIRE
 *
 * @see https://github.com/nrk/predis
 * composer require simplepie/simplepie
 *
 * @REGISTER:
 *
 * $app->register(new \e1\providers\SimplePie\SimplePieServiceProvider(), [
 *      'cache.location' => __DIR__ . '/framework/cache',
 *      'cache.life' => 3600,
 *      'cache.disabled' => false,
 *      'strip_html_tags.disabled' => false,
 *      'strip_html_tags.tags' => ['base', 'blink', 'body', 'doctype', 'embed', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'object', 'param', 'script', 'style'],
 *      'strip_attribute.disabled' => false,
 *      'strip_attributes.tags' => ['bgsound', 'class', 'expr', 'id', 'style', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur', 'lowsrc', 'dynsrc'],
 * ]);
 *
 * @USAGE:
 *
 *
 */
class SimplePieServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    protected $app;
    protected $client;

    public function boot(Application $app)
    {
        $app['simplePie.init'];
    }

    public function register(Container $app)
    {
        $this->app = $app;
        $app['simplePie'] = $this;

        $app['simplePie.init'] = function () use ($app) {

            $this->client = new \SimplePie();

            if ($app['simplePie.cache.disabled']) {
                $this->client->enable_cache(false);
            } else {
                $this->client->set_cache_location($app['simplePie.cache.location']);
                $this->client->set_cache_duration($app['simplePie.cache.life']);
            }

            if (!$app['simplePie.strip_html_tags.disabled'] && !empty($app['simplePie.strip_html_tags.tags']) && is_array($app['simplePie.strip_html_tags.tags'])) {
                $this->client->strip_htmltags($app['simplePie.strip_html_tags.tags']);
            } else {
                $this->client->strip_htmltags(false);
            }

            if (!$app['simplePie.strip_attribute.disabled'] && !empty($app['simplePie.strip_attribute.tags']) && is_array($app['simplePie.strip_attribute.tags'])) {
                $this->client->strip_attributes($app['simplePie.strip_attribute.tags']);
            } else {
                $this->client->strip_attributes(false);
            }
        };
    }

    public function feed(array $feed_url = [], int $limit = 0, bool $force_feed = false): \SimplePie
    {
        $this->client->set_item_limit($limit);
        $this->client->set_feed_url($feed_url);
        $this->client->force_feed($force_feed);

        $this->client->init();
        $this->client->handle_content_type();

        return $this->client;
    }
}