<?php

namespace App\AdminAddons;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminDefault extends Controller
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

    public function notFound($navigation)
    {
        return $this->render('panel/not-found.html.twig', [
            'navigation_links' => $navigation,
        ]);
    }

}
