<?php

namespace Core;

class View
{
    public static function render($path, $args = [])
    {


        extract($args, EXTR_SKIP);
        $filepath = "../App/View/$path";
        if (is_readable($filepath)) {
            require $filepath;
        } else {
            echo 'file $file cannon be read';
        }
    }

    public static function renderTemplate($file, $args = [])
    {
        static $twig = null;
        if (!$twig) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/View');
            $twig = new \Twig\Environment($loader, [
                'cache' => '../Cache',
            ]);
        }
        echo $twig->render($file, $args);
    }
}
