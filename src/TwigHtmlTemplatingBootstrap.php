<?php namespace Monolith\Twig;

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
        $container->bind(\Twig_Environment::class, function ($r) {

            $templatePaths = getenv('TWIG_TEMPLATE_PATHS');

            if (is_array($templatePaths)) {
                $templatePaths = array_map(function($path) {
                    return $this->rootPath . $path;
                }, $templatePaths);
            } else {
                $templatePaths = $this->rootPath . $templatePaths;
            }

            $loader = new Twig_Loader_Filesystem($templatePaths);

            return new Twig_Environment($loader, [
                'cache' => getenv('TWIG_CACHE_PATH'),
                'auto_reload' => strtolower(getenv('TWIG_AUTO_RELOAD')) == 'true'
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