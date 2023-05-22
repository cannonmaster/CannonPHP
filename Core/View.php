<?php

namespace Core;

class View
{
    /**
     * Renders a view file.
     *
     * @param string $path The path to the view file.
     * @param array $args The arguments to be extracted and passed to the view file.
     */
    public static function render($path, $args = [])
    {
        extract($args, EXTR_SKIP);
        $filepath = "../App/View/$path";
        if (is_readable($filepath)) {
            require $filepath;
        } else {
            echo "file $filepath cannot be read";
        }
    }

    /**
     * Renders a template file using Twig.
     *
     * @param string $file The template file to render.
     * @param array $args The arguments to be passed to the template.
     * @return string The rendered template.
     */
    public static function renderTemplate($file, $args = [])
    {
        static $twig = null;
        $theme = \App\Config::theme ?: 'default';
        if (!$twig) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . "/App/View/$theme");
            $twig = new \Twig\Environment($loader, [
                'cache' => '../Cache',
            ]);
        }
        return $twig->render($file, $args);
    }
}
