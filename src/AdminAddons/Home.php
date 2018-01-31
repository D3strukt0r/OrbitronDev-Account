<?php

namespace App\AdminAddons;

use Psr\Container\ContainerInterface;

class Home
{
    public static function __setupNavigation()
    {
        return [
            'type'   => 'link',
            'parent' => 'root',
            'id'     => 'home',
            'title'  => 'Overview',
            'href'   => 'home',
            'icon'   => 'fa fa-fw fa-home',
            'view'   => 'Home::home',
        ];
    }

    public static function __callNumber()
    {
        return 1;
    }

    public static function home(ContainerInterface $container)
    {
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        return $container->get('twig')->render('panel/home.html.twig', array(
            'user' => $user,
        ));
    }

}
