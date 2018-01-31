<?php

namespace App\AdminAddons;

use Psr\Container\ContainerInterface;

class Payment
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

    public static function plans(ContainerInterface $container)
    {
        $twig = $container->get('twig');
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        return $twig->render('panel/plans.html.twig', array(
            'current_user' => $user,
        ));
    }

    public static function payment(ContainerInterface $container)
    {
        return '';
    }
}
