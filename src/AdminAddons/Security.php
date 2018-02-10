<?php

namespace App\AdminAddons;

use App\Form\DeleteAccountType;
use App\Helper\AccountHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Security extends Controller
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

    public function inactivity()
    {
        return new Response();
    }

    public function loginLog()
    {
        return new Response();
    }

    public function deleteAccount(Request $request, $navigation)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $deleteAccountForm = $this->createForm(DeleteAccountType::class);

        $deleteAccountForm->handleRequest($request);
        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) {
            AccountHelper::removeUser($em, $user);
            header('Location: '.$this->generateUrl('logout'));
            exit;
        }

        return $this->render('panel/delete-account.html.twig', [
            'navigation_links'    => $navigation,
            'delete_account_form' => $deleteAccountForm->createView(),
        ]);
    }
}
