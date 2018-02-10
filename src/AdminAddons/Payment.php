<?php

namespace App\AdminAddons;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class Payment extends Controller
{
    public static function __setupNavigation()
    {
        return [
            [
                'type'   => 'group',
                'parent' => 'root',
                'id'     => 'payment',
                'title'  => 'Billing',
                'icon'   => 'fa fa-fw fa-credit-card',
            ],
            [
                'type'   => 'link',
                'parent' => 'payment',
                'id'     => 'plans',
                'title'  => 'Plans',
                'href'   => 'plans',
                'view'   => 'Payment::plans',
            ],
            [
                'type'   => 'link',
                'parent' => 'payment',
                'id'     => 'payment_methods',
                'title'  => 'Payment methods',
                'href'   => 'payment',
                'view'   => 'Payment::payment',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 20;
    }

    public function plans($navigation)
    {
        return $this->render('panel/plans.html.twig', [
            'navigation_links' => $navigation,
            'current_user'     => $this->getUser(),
        ]);
    }

    public function payment()
    {
        return new Response();
    }
}
