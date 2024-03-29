<?php namespace Monolith\Twig;

use Monolith\Configuration\Config;
use Monolith\Collections\MutableCollection;
use Monolith\DependencyInjection\Container;
use Monolith\ComponentBootstrapping\ComponentBootstrap;

final class TwigHtmlTemplatingBootstrap implements ComponentBootstrap
{
    private string $rootPath;

    public function __construct($rootPath)
    {
        $this->rootPath = realpath($rootPath) . '/';
    }

    public function bind(Container $container): void
    {
        $container->singleton(
            TwigTemplatePaths::class, function ($r) {
            /** @var Config $config */
            $config = $r(Config::class);

            $paths = $config->get('TWIG_TEMPLATE_PATHS');

            if (stristr($paths, ':')) {
                $pathArray = explode(':', $paths);
            } else {
                $pathArray = [$paths];
            }

            $fullyQualifiedTemplatePaths = array_map(
                function ($path) {
                    return $this->rootPath . $path;
                }, $pathArray
            );

            return new TwigTemplatePaths($fullyQualifiedTemplatePaths);
        }
        );

        $container->singleton(
            Twig::class, function ($r) {
            /** @var Config $config */
            $config = $r(Config::class);

            /** @var MutableCollection $templatePaths */
            $templatePaths = $r(TwigTemplatePaths::class);

            $loader = new \Twig\Loader\FilesystemLoader($templatePaths->toArray());

            $environment = new \Twig\Environment(
                $loader, [
                'cache' => $config->get('TWIG_CACHE_PATH'),
                'auto_reload' => strtolower($config->get('TWIG_AUTO_RELOAD')) == 'true',
            ]
            );

            return new Twig($environment);
        }
        );
    }

    public function init(Container $container): void
    {
    }
}