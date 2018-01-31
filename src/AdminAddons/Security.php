<?php

namespace App\AdminAddons;

use App\Entity\User;
use App\Form\DeleteAccountType;
use App\Helper\AccountHelper;
use Psr\Container\ContainerInterface;

class Security
{
    public static function __setupNavigation()
    {
        return [
            [
                'type'   => 'group',
                'parent' => 'root',
                'id'     => 'security',
                'title'  => 'Security',
                'icon'   => 'fa fa-fw fa-lock',
            ],
            [
                'type'   => 'link',
                'parent' => 'security',
                'id'     => 'inactivity',
                'title'  => 'Inactivity',
                'href'   => 'inactivity',
                'view'   => 'Security::inactivity',
            ],
            [
                'type'   => 'link',
                'parent' => 'security',
                'id'     => 'log',
                'title'  => 'Login log',
                'href'   => 'login-log',
                'view'   => 'Security::loginLog',
            ],
            [
                'type'   => 'link',
                'parent' => 'security',
                'id'     => 'delete',
                'title'  => sprintf('%sDelete Account%s', '<b><span class="text-danger">', '</span></b>'),
                'href'   => 'delete-account',
                'view'   => 'Security::deleteAccount',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 30;
    }

    public static function inactivity(ContainerInterface $container)
    {
        return '';
    }

    public static function loginLog(ContainerInterface $container)
    {
        return '';
    }

    public static function deleteAccount(ContainerInterface $container)
    {
        $em = $container->get('doctrine')->getManager();
        $form = $container->get('form.factory');
        $request = $container->get('request_stack')->getCurrentRequest();
        $twig = $container->get('twig');
        $router = $container->get('router');
        /** @var \App\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();

        $deleteAccountForm = $form->create(DeleteAccountType::class);

        $deleteAccountForm->handleRequest($request);
        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) {
            AccountHelper::removeUser($em, $user);
            header('Location: '.$router->generate('logout'));
            exit;
        }

        return $twig->render('panel/delete-account.html.twig', array(
            'delete_account_form' => $deleteAccountForm->createView(),
        ));
    }
}
