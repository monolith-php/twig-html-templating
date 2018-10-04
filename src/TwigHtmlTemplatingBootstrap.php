<?php namespace Monolith\Twig;

use Monolith\ComponentBootstrapping\ComponentBootstrap;
use Monolith\DependencyInjection\Container;
use Twig_Environment;
use Twig_Loader_Filesystem;

final class TwigHtmlTemplatingBootstrap implements ComponentBootstrap
{
    public function bind(Container $container): void
    {
        $container->bind(\Twig_Environment::class, function ($r) {
            $loader = new Twig_Loader_Filesystem(getenv('TWIG_TEMPLATE_PATHS'));

            return new Twig_Environment($loader, [
                'cache' => getenv('TWIG_CACHE_PATH'),
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