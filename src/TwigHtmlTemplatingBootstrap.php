<?php namespace Monolith\Twig;

use Monolith\Configuration\Config;
use Monolith\Collections\MutableCollection;
use Monolith\ComponentBootstrapping\ComponentBootstrap;
use Monolith\DependencyInjection\Container;
use Twig_Environment;
use Twig_Loader_Filesystem;

final class TwigHtmlTemplatingBootstrap implements ComponentBootstrap
{
    private $rootPath;

    /** @var Config */
    private $config;

    public function __construct($rootPath)
    {
        $this->rootPath = realpath($rootPath) . '/';
    }

    public function bind(Container $container): void
    {
        $this->config = $container(Config::class);

        $container->singleton(TwigTemplatePaths::class, function ($r) {
            $paths = $this->config->get('TWIG_TEMPLATE_PATHS');

            if (stristr($paths, ':')) {
                $pathArray = explode(':', $paths);
            } else {
                $pathArray = [$paths];
            }

            $fullyQualifiedTemplatePaths = array_map(function ($path) {
                return $this->rootPath . $path;
            }, $pathArray);

            return new TwigTemplatePaths($fullyQualifiedTemplatePaths);
        });

        $container->singleton(Twig::class, function ($r) {
            /** @var MutableCollection $templatePaths */
            $templatePaths = $r(TwigTemplatePaths::class);

            $loader = new Twig_Loader_Filesystem($templatePaths->toArray());

            $environment = new Twig_Environment($loader, [
                'cache'       => $this->config->get('TWIG_CACHE_PATH'),
                'auto_reload' => strtolower($this->config->get('TWIG_AUTO_RELOAD')) == 'true',
            ]);

            return new Twig($environment);
        });
    }

    public function init(Container $container): void
    {
    }
}
