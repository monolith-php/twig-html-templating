<?php namespace Monolith\Twig;

use Twig\Environment;

final class Twig
{
    /** @var Environment */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render($template, array $variables = []): string
    {
        $template = $this->twig->load($template);
        return $template->render($variables);
    }

    public function renderForm($form, $template, array $variables = []): string
    {
        $this->twig->addGlobal('form_model', $form);
        $template = $this->twig->load($template);
        
        return $template->render($variables);
    }
}