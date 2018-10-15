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

            $fullyQualifiedTemplatePaths = array_map(function ($path) {
                return $this->rootPath . $path;
            }, $paths);

            $templatePaths = new TwigTemplatePaths($fullyQualifiedTemplatePaths);

            $templatePaths->add(__DIR__ . '/../templates');

            return $templatePaths;
        });

        $container->bind(\Twig_Environment::class, function ($r) {
            /** @var MutableCollection $templatePaths */
            $templatePaths = $r(TwigTemplatePaths::class);

            $loader = new Twig_Loader_Filesystem($templatePaths->toArray());

            return new Twig_Environment($loader, [
                'cache'       => getenv('TWIG_CACHE_PATH'),
                'auto_reload' => strtolower(getenv('TWIG_AUTO_RELOAD')) == 'true',
            ]);
        });
    }

    public function init(Container $container): void
    {
    }
}