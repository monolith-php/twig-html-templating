<?php namespace spec\Monolith\Twig;

use Monolith\ComponentBootstrapping\ComponentLoader;
use Monolith\Configuration\ConfigurationBootstrap;
use Monolith\DependencyInjection\Container;
use Monolith\Twig\Twig;
use Monolith\Twig\TwigHtmlTemplatingBootstrap;
use PhpSpec\ObjectBehavior;

class TwigSpec extends ObjectBehavior
{
    function bootstrapMonolith(): Container
    {
        $container = new Container;
        $loader = new ComponentLoader($container);
        $loader->register(
            new ConfigurationBootstrap('spec/'),
            new TwigHtmlTemplatingBootstrap
        );
        $loader->load();
        return $container;
    }

    function let()
    {
        $container = $this->bootstrapMonolith();

        $this->beConstructedWith(
            $container->get(\Twig_Environment::class)
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Twig::class);
    }

    function it_can_render_twig_templates()
    {
        $this->render('example.html.twig')->shouldBe("Hello!");
    }

    function it_can_render_twig_templates_with_variable_bindings()
    {
        $this->render('example-with-variables.html.twig', ['name' => 'Mitchell'])->shouldBe("Hello Mitchell");
    }
}
