<?php namespace Monolith\Twig;

use Twig\Environment;

final class Twig
{
    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function environment(): Environment
    {
        return $this->environment;
    }

    public function render(string $path, array $arguments = [])
    {
        return $this->environment->render($path, $arguments);
    }

    public function renderForm($formModel, array $arguments = [])
    {
        $this->environment->addGlobal('form_model', $formModel);
        return $this->render($formModel->twigTemplatePath(), $arguments);
    }
}