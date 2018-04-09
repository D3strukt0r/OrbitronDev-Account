<?php

namespace App\Controller\Panel;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public static function __setupNavigation()
    {
        return [
            'type' => 'link',
            'parent' => 'root',
            'id' => 'home',
            'title' => 'Overview',
            'href' => 'home',
            'icon' => 'hs-admin-panel',
            'view' => 'HomeController::home',
        ];
    }

    public static function __callNumber()
    {
        return 1;
    }

    public function home($navigation)
    {
        return $this->render('panel/home.html.twig', [
            'navigation_links' => $navigation,
        ]);
    }
}
