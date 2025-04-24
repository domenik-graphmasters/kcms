<?php

namespace vcms;

use vcms\module\LibModule;

class LibTemplateRenderer
{
    function __construct(
        private LibModuleHandler $libModuleHandler,
        private LibGlobal $libGlobal
    ) {}

    /**
     * Renders a template into a string.
     * @param string $template name of the template to render
     * @param array $context map of string to string to be used in the template
     * @return ?string the rendered template or null if an error occurred
     */
    function render(string $template, array $context): ?string
    {
        $moduleTemplateDirectories = array_map(function (LibModule $module) {
            return $module->getPath() . "/templates";
        }, $this->libModuleHandler->getModules());
        $moduleTemplateDirectories[] = "./vendor/vcms/layout";
        $moduleTemplateDirectories = array_filter(
            $moduleTemplateDirectories,
            function ($path) {
                return $path != null && is_dir($path);
            }
        );

        $loader = new \Twig\Loader\FilesystemLoader($moduleTemplateDirectories);
        $twig = new \Twig\Environment($loader, [
            "cache" => "./temp/twig/compilation_cache",
        ]);

        try {
            return $twig->render($template, $context);
        } catch (\Twig\Error\LoaderError $e) {
            $this->libGlobal->errorTexts[] = $e->getMessage();
            return null;
        } catch (\Twig\Error\RuntimeError $e) {
            $this->libGlobal->errorTexts[] = $e->getMessage();
            return null;
        } catch (\Twig\Error\SyntaxError $e) {
            $this->libGlobal->errorTexts[] = $e->getMessage();
            return null;
        }
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
