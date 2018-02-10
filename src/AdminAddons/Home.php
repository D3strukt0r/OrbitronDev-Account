<?php

namespace App\AdminAddons;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Home extends Controller
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

    public function home($navigation)
    {
        return $this->render('panel/home.html.twig', array(
            'navigation_links' => $navigation,
            'user'             => $this->getUser(),
        ));
    }

}
