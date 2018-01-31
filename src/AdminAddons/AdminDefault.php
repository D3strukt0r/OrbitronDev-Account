<?php

namespace App\AdminAddons;

use Psr\Container\ContainerInterface;

class AdminDefault
{
    public static function __setupNavigation()
    {
        return [
            'type'    => 'group',
            'parent'  => 'root',
            'id'      => 'null',
            'title'   => null,
            'display' => false,
        ];
    }

    public static function __callNumber()
    {
        return 0;
    }

    public static function notFound(ContainerInterface $container)
    {
        $twig = $container->get('twig');

        return $twig->render('panel/not-found.html.twig');
    }

}
