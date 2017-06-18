<?php

namespace e1\providers\xPath;

use DOMDocument;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class xPathServiceProvider
 * @package e1\providers\xPath
 *
 * @property \e1\Application $app
 */
class xPathServiceProvider implements ServiceProviderInterface
{
    protected $app;

    public function register(Container $app)
    {
        $this->app = $app;
        $app['xPath'] = $this;

        $app['xPath.DomDocument'] = $app->protect(function () {
            return $this->getDomDocument();
        });

        $app['xPath.DomXPath'] = $app->protect(function (DomDocument $domDocument) {
            return $this->getDomXPath($domDocument);
        });

        $app['xPath.parse'] = $app->protect(function (string $url) {

            libxml_use_internal_errors(true);

            $content = $this->getContent($url);

            $document = $this->getDomDocument();

            $document->loadHTML($content);

            return $this->getDomXPath($document);

        });
    }

    public function getContent(string $url)
    {
        $content = file_get_contents($url);

        foreach ($http_response_header as $c => $h) {
            if (stristr($h, 'content-encoding') and stristr($h, 'gzip')) {
                $content = gzinflate(substr($content, 10, -8));
            }
        }

        return $content;
    }

    public function getDomXPath(\DOMDocument $domDocument)
    {
        return new \DomXPath($domDocument);
    }

    public function getDomDocument($version = null, $encoding = null)
    {
        return new \DOMDocument($version, $encoding);
    }
}