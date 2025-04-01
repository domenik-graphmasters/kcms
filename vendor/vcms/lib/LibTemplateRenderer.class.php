<?php

namespace vcms;

use vcms\module\LibModule;

class LibTemplateRenderer
{
    function __construct(private LibModuleHandler $libModuleHandler) {}

    /**
     * Renders a template into a string.
     * @param string $template name of the template to render
     * @param array $context map of string to string to be used in the template
     * @return string the rendered template
     */
    function render(string $template, array $context): string
    {
        $moduleTemplateDirectories = array_map(function (LibModule $module) {
            return $module->getPath() . "/templates";
        }, $this->libModuleHandler->getModules());
        $moduleTemplateDirectories = array_filter(
            $moduleTemplateDirectories,
            function ($path) {
                return $path != null && is_dir($path);
            }
        );
        $moduleTemplateDirectories[] = "./vendor/vcms/layout";

        $loader = new \Twig\Loader\FilesystemLoader($moduleTemplateDirectories);
        $twig = new \Twig\Environment($loader, [
            "cache" => "./temp/twig/compilation_cache",
        ]);
        // TODO: Handle exceptions
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
