<?php namespace Monolith\Twig;

use Monolith\Collections\MutableCollection;
use Monolith\ComponentBootstrapping\ComponentBootstrap;
use Monolith\DependencyInjection\Container;
use Twig_Environment;
use Twig_Loader_Filesystem;

final class TwigHtmlTemplatingBootstrap implements ComponentBootstrap
{
    private $rootPath;

    public function __construct($rootPath)
    {
        $this->rootPath = realpath($rootPath) . '/';
    }

    public function bind(Container $container): void
    {
        $container->singleton(TwigTemplatePaths::class, function ($r) {
            $paths = getenv('TWIG_TEMPLATE_PATHS');

            if ( ! is_array($paths)) {
                $paths = [$paths];
            }

            return new TwigTemplatePaths($paths);
        });

        $container->bind(\Twig_Environment::class, function ($r) {

            /** @var MutableCollection $templatePaths */
            $templatePaths = $r(TwigTemplatePaths::class);

            $fullyQualifiedTemplatePaths = $templatePaths->map(function($path) {
                return $this->rootPath . $path;
            });

            $loader = new Twig_Loader_Filesystem($fullyQualifiedTemplatePaths->toArray());

            return new Twig_Environment($loader, [
                'cache'       => getenv('TWIG_CACHE_PATH'),
                'auto_reload' => strtolower(getenv('TWIG_AUTO_RELOAD')) == 'true',
            ]);
        });

        $container->bind(Twig::class, function ($r) {
            return new Twig($r(\Twig_Environment::class));
        });
    }

    public function init(Container $container): void
    {
    }
}