<?php

namespace vcms;

class LibTemplateRenderer
{
    /**
     * Renders a template into a string.
     * @param string $template name of the template to render
     * @param array $context map of string to string to be used in the template
     * @return string the rendered template
     */
    function render(string $template, array $context): string
    {
        $loader = new \Twig\Loader\FilesystemLoader(["./vendor/vcms/layout"]);
        $twig = new \Twig\Environment($loader, [
            "cache" => "./temp/twig/compilation_cache",
        ]);
        return $twig->render($template, $context);
    }

    /**
     * Renders a template into a string and outputs it to the browser.
     * @param string $template name of the template to render
     * @param array $context map of string to string to be used in the template
     * @return void
     */
    function display(string $template, array $context): void
    {
        echo $this->render($template, $context);
    }
}
